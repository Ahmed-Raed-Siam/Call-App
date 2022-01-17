<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCollectionResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
//        $services = Service::paginate(15);
        $services = Service::orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate();
//        return Response::json(ServiceResource::collection($services), 200);
        return Response::json(new ServiceCollectionResource($services), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
//        dd(
//            $request->all(),
//            $request->hasFile('icon_path'),
//            $request->post('order'),
//            Storage::disk('uploads')->allDirectories(),
//            Storage::disk('uploads')->allFiles(),
//        );

        $request->validate([
            'name' => ['required', 'string', 'unique:services,name'],
            'description' => ['required', 'string', 'unique:services,description'],
            'icon_path' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'order' => ['nullable', 'numeric'],
        ]);

//        dd(
//            $request->all(),
//            $request->hasFile('icon_path'),
//            $request->post('order'),
//            Storage::disk('uploads')->allDirectories(),
//            Storage::disk('uploads')->allFiles(),
//        );

        $service_name = $request->post('name');
        $service_description = $request->post('description');
        if ($request->post('order')):
            $service_order = (int)$request->post('order');
        else:
            $service_order = 0;
        endif;

        $icon_path = '';
        $new_service_name = str_replace(' ', '_', $service_name);

        if ($request->hasFile('icon_path')):
            $file_name = $request->file('icon_path')->getClientOriginalName();// file name with extension
            $request_file_path = $new_service_name . '/' . $file_name;
//            dd(
//                $request_file_path,
//                Storage::disk('uploads')->allDirectories(),
//                Storage::disk('uploads')->allFiles(),
//                Storage::disk('uploads')->files($user_name),
//                Storage::disk('uploads')->files($new_service_name),
//                Storage::disk('uploads')->exists($request_file_path),
//                in_array($request_file_path, Storage::disk('uploads')->files($user_name)),
//            );
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($new_service_name))):
                $icon_path = $request_file_path;
            else:
//                $icon_path = $request->file('icon_path')->store($new_service_name, [
//                    'disk' => 'uploads',
//                ]);
                $icon_path = $request->file('icon_path')->storeAs(
                    $service_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        endif;

//        dd(
////            $request->all(),
//            $request->hasFile('icon_path'),
//            $request->post('icon_path'),
//            Storage::disk('uploads')->allDirectories(),
//            'allFiles',
//            Storage::disk('uploads')->allFiles(),
//            Storage::disk('uploads')->allFiles($new_service_name),
//            'uploads',
//            Storage::disk('uploads')->files($new_service_name),
//            $icon_path,
//            Storage::disk('uploads')->exists($request_file_path),
//            in_array($request_file_path, Storage::disk('uploads')->allFiles($new_service_name)),
//        );

        $service = Service::create([
            'name' => $service_name,
            'description' => $service_description,
            'icon_path' => $icon_path,
            'order' => $service_order,
        ]);

//        dd(
//            $service,
//            $icon_path,
//            $request->all(),
//            $request->hasFile('icon_path'),
//            $icon_path,
//            url('/uploads/' . $service->icon_path),
//            Storage::disk('uploads')->allDirectories(),
//            Storage::disk('uploads')->allFiles(),
//        );

        $message = 'New service added successfully>';

        return Response::json([
            'message' => $message,
            'service' => ServiceResource::make($service),
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $service = Service::find($id);
        $msg = '';
        if ($service === null):
            $msg = 'Wrong Request ! Service Not Exists.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;

        $service_types = $service->service_types()->get();
//        if (count($service_types) > 0):
//            $service_types = 'no service type to this service';
//        endif;

        if (empty($service_types) || count($service_types) === 0):
            $service_types = 'no service type to this service';
        endif;
        // OR This
//        $service_types = $service->with(['service_types'])->where('id', '=', $id)->first()->service_types;
//        dd(
//            $service,
//            $service->service_types()->get(),
//            $service->with(['service_types'])->where('id', '=', $id)->get(),
//            $service->with(['service_types'])->where('id', '=', $id)->first()->service_types,
//        );
        return Response::json([
            'service' => ServiceResource::make($service),
//            'count_service_types' => count($service_types),
            'service_types' => $service_types,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $service = Service::find($id);

        if ($service === null):
            $msg = 'Wrong Request ! Service Not Exists.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;

//        dd(
//            $request,
//            $request->all(),
//            $request->hasFile('icon_path'),
//            $request->post('order'),
//            Storage::disk('uploads')->allDirectories(),
//            Storage::disk('uploads')->allFiles(),
//        );

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('services')->ignore($service->id),
            ],
            'description' => [
                'required',
                'string',
                Rule::unique('services')->ignore($service->id),
            ],
            'icon_path' => ['nullable', 'image', 'mimes:jpg,bmp,png',
                Rule::unique('services')->ignore($service->id)
            ],
            'order' => ['nullable', 'numeric'],
        ]);

        $service_types = $service->service_types()->get();

        $service_name_model = $service->name;
        $service_description_model = $service->description;
        $service_icon_path_model = $service->icon_path;
        $service_order_model = $service->order;

//        dd(
//            $request->all(),
//            $request->hasFile('icon_path'),
//            $request->post('order'),
//            Storage::disk('uploads')->allDirectories(),
//            Storage::disk('uploads')->allFiles(),
//        );

        if ($request->post('order')):
            $request_service_order = (int)$request->post('order');
        else:
            $request_service_order = $service_order_model;
        endif;

        $request_service_name = $request->post('name');
        $request_service_description = $request->post('description');

        $request_icon_path = '';
        $request_service_name = str_replace(' ', '_', $request_service_name);

        if ($request->hasFile('icon_path')):
            $file_name = $request->file('icon_path')->getClientOriginalName();// file name with extension
            $request_file_path = $request_service_name . '/' . $file_name;
            if ($request_file_path !== $service_icon_path_model):
                $request->validate([
                    'icon_path' => [
                        'nullable',
                        'image',
                        'mimes:jpg,bmp,png',
                        Rule::unique('services')->ignore($service->id)
                    ],
                ]);
            endif;
//            dd(
//                $request_service_name,
//                '$service_name_model',
//                $service_name_model,
//                '$file_name',
//                $file_name,
//                '$request_file_path',
//                $request_file_path,
//                Storage::disk('uploads')->exists($request_file_path),
//                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_name), true),
//                '$request_icon_path',
//                $request_icon_path,
//            );
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_name), false)):
                $request_icon_path = $request_file_path;
//                dd(
//                    'if Exists',
//                    $request_service_name,
//                    '$service_name_model',
//                    $service_name_model,
//                    '$file_name',
//                    $file_name,
//                    '$request_file_path',
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_name), true),
//                    '$request_icon_path',
//                    $request_icon_path,
//                );
            else:
//                dd(
//                    $request_service_name,
//                    '$service_name_model',
//                    $service_name_model,
//                    '$file_name',
//                    $file_name,
//                    '$request_file_path',
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_name), true),
//                    '$request_icon_path',
//                    $request_icon_path,
//                    Storage::disk('uploads')->exists($request_icon_path),
//                );

                Storage::disk('uploads')->deleteDirectory($service_name_model);
                Storage::disk('uploads')->delete($service_icon_path_model);
                $request_icon_path = $request->file('icon_path')->storeAs(
                    $request_service_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
//                Storage::disk('uploads')->move($service_icon_path_model, Storage::disk('uploads')->get($request_icon_path).'/');

//                rename($service_icon_path_model, $request_icon_path);

                // Update Service Types to this service if There!
//                if ($service_types->count() > 0):
////                    dd(
////                        $service_types,
////                        $service_types->first(),
////                        $service_types_ids = $service_types->pluck('id'),
////
////                        '$request_service_name',
////                        $request_service_name,
////                        '$service_icon_path_model',
////                        $service_icon_path_model,
////                        '$service_types->first()->icon_path',
////                        $service_types_icon_path = $service_types->first()->icon_path,
////                        'str_replace',
////                        $service_icon_path_array = explode('/', $service_types_icon_path),
////                        $base_service_icon_path = $service_icon_path_array[0],
////                        str_replace($base_service_icon_path, $request_service_name, $service_types_icon_path),
////                        ServiceType::whereIn('id', $service_types_ids)->update([
////                            'icon_path' => str_replace($service_icon_path_model, $request_service_name, $service_types->first()->icon_path),
////                        ]),
////                    );
//
////                    $service_types_ids = $service_types->pluck('id');
////                    ServiceType::whereIn('id', $service_types_ids)->update([
////                        'icon_path' => str_replace($service_icon_path_model, $request_service_name, $service_types->first()->icon_path),
////                    ]);
//
//
////                    foreach ($service_types as $service_type):
////                        $service_types_icon_path = $service_type->icon_path;
////                        $service_icon_path_array = explode('/', $service_types_icon_path);
////                        $base_service_icon_path = $service_icon_path_array[0];
////                        $new_base_service_type_icon_path = str_replace($base_service_icon_path, $request_service_name, $service_types_icon_path);
////                        $service_type->icon_path = $new_base_service_type_icon_path;
////                        $service_type->save();
////                    endforeach;
//                endif;

            endif;
        else:
            Storage::disk('uploads')->delete($service_icon_path_model);
        endif;

        $service->update([
            'name' => $request_service_name,
            'description' => $request_service_description,
            'icon_path' => $request_icon_path,
            'order' => $request_service_order,
        ]);

        $message = "$service_name_model service updated to $request_service_name successfully>";

        return Response::json([
            'message' => $message,
            'service' => ServiceResource::make($service),
            'service_types' => $service_types,
        ], 200);


    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $service = Service::find($id);

        $message = '';
        if ($service === null):
            $message = 'Wrong Request ! Service Not Exists.';
            return Response::json([
                'message' => $message,
            ], 200);
        endif;

        $service_name = $service->name;
        $service_types = $service->service_types();
        $service_icon_path = $service->icon_path;

        $service_icon_path_array = explode('/', $service_icon_path);
        $base_service_icon_path = $service_icon_path_array[0] . '/';

        if (Storage::disk('uploads')->exists($service_icon_path) ||
            in_array($service_icon_path, Storage::disk('uploads')->allFiles($service_name), true)):
            Storage::disk('uploads')->deleteDirectory($base_service_icon_path);
            Storage::disk('uploads')->delete($service_icon_path);
        endif;

        if ($service_types->count() > 0):
            $deleted_files = $service_types->get()->implode('name', ' , ');
            $error_msg = "You can't delete this service because it related to {$service_types->count()} service types.\n $deleted_files";
            return Response::json([
                    'message' => $error_msg
                ]
                , 403);
        endif;

        $service->forceDelete();

        $message = "$service_name service deleted successfully>";
        return Response::json([
                'message' => $message
            ]
            , 200);

    }
}
