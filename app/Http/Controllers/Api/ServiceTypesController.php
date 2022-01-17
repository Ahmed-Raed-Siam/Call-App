<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceTypesCollectionResource;
use App\Http\Resources\ServiceTypesResource;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $service_types = ServiceType::orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(3);
//        return Response::json($service_types, 200);
        return Response::json(new ServiceTypesCollectionResource($service_types), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:service_types,name'],
            'description' => ['required', 'string', 'unique:service_types,description'],
            'icon_path' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'service_id' => ['required', 'string', 'numeric', 'exists:services,id'],
            'order' => ['nullable', 'numeric'],
        ]);

        $request_service_type_name = $request->post('name');
        $request_service_type_description = $request->post('description');
        $request_service_type_id = $request->post('service_id');

        $service = Service::find($request_service_type_id);
        $msg = '';
        if ($service === null):
            $msg = 'Wrong Request ! Service Not Exists.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;

        if ($request->post('order')):
            $request_service_type_order = (int)$request->post('order');
        else:
            $request_service_type_order = 0;
        endif;

        $icon_path = '';
        $service_name = $service->name;
        $service_icon_path = $service->icon_path;

        $request_service_type_name = str_replace(' ', '_', $request_service_type_name);
        if ($request->hasFile('icon_path')):
            $file_name = $request->file('icon_path')->getClientOriginalName();// file name with extension
            $request_file_path = $request_service_type_name . '/' . $file_name;

            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_type_name))):
                $icon_path = $request_file_path;
            else:
                $icon_path = $request->file('icon_path')->storeAs(
                    $request_service_type_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        endif;

        $service_type = $service->service_types()->create([
            'name' => $request_service_type_name,
            'description' => $request_service_type_description,
            'icon_path' => $icon_path,
            'service_id' => $request_service_type_id,
            'order' => $request_service_type_order,
        ]);

        $message = "New service type added to $service->name successfully>";

        return Response::json([
            'message' => $message,
            'service_type' => ServiceTypesResource::make($service_type),
//            'service' => ServiceResource::make($service),
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
        $service_type = ServiceType::find($id);
        $msg = '';
        if ($service_type === null):
            $msg = 'Wrong Request ! Service Not Exists.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;
//        $service = $service_type->service()->get();
        $service = $service_type->service;
        $products = $service_type->products;
        if (empty($products) === false && $products->count() > 0):
            $products = ProductResource::collection($products);
        else:
            $products = 'This service type has no products yet.';
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
            'service_type' => ServiceTypesResource::make($service_type),
            'products' => $products,
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
        $service_types = ServiceType::find($id);

        $msg = '';
        if ($service_types === null):
            $msg = 'Wrong Request ! This Service Type Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('service_types')->ignore($service_types->id),
            ],
            'description' => [
                'required',
                'string',
                Rule::unique('service_types')->ignore($service_types->id),
            ],
            'icon_path' => ['nullable', 'image', 'mimes:jpg,bmp,png',
                Rule::unique('service_types')->ignore($service_types->id)
            ],
            'order' => ['nullable', 'numeric'],
            'service_id' => ['required', 'string', 'numeric', 'exists:services,id'],

        ]);

        $service = $service_types->service;
        $service_name = $service->name;
        $service_icon_path = $service->icon_path;

        $service_types_name = $service_types->name;
        $service_types_order = $service_types->order;
        $service_types_icon_path = $service_types->icon_path;

        if ($request->post('order')):
            $request_service_types_order = (int)$request->post('order');
        else:
            $request_service_types_order = $service_types_order;
        endif;

        $request_service_type_name = $request->post('name');
        $request_service_type_description = $request->post('description');
        $request_service_type_service_id = $request->post('service_id');

        $request_icon_path = '';
        $request_service_types_name = str_replace(' ', '_', $request_service_type_name);
        $service_file_path = substr($service_icon_path, 0, strrpos($service_icon_path, '/'));

        if ($request->hasFile('icon_path')):
            $file_name = $request->file('icon_path')->getClientOriginalName();// file name with extension
            $request_file_path = $request_service_types_name . '/' . $file_name;
            if ($request_file_path !== $service_types_icon_path):
                $request->validate([
                    'icon_path' => [
                        'nullable',
                        'image',
                        'mimes:jpg,bmp,png',
                        Rule::unique('service_types')->ignore($service_types->id)
                    ],
                ]);
            endif;
//            dd(
//                $service_icon_path,
//                $service_file_path,
//                $service_file_path . '/' . $request_service_types_name . '/' . $file_name,
//                $request_service_type_name,
//                '$service_types_name',
//                $service_types_name,
//                '$file_name',
//                $file_name,
//                '$request_file_path',
//                $request_file_path,
//                'Storage--',
//                Storage::disk('uploads')->allFiles($request_service_type_name),
//                'Storage',
//                Storage::disk('uploads')->exists($request_file_path),
//                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_type_name), true),
//                '$request_icon_path',
//                $request_icon_path,
//            );
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_types_name), false)):
                $request_icon_path = $request_file_path;
//                dd(
//                    'if Exists',
//                    $request_service_type_name,
//                    '$service_types_name',
//                    $service_types_name,
//                    '$file_name',
//                    $file_name,
//                    '$request_file_path',
//                    $request_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_type_name), true),
//                    '$request_icon_path',
//                    $request_icon_path,
//                );
            else:
//                dd(
//                    $request_service_type_name,
//                    '$service_types_name',
//                    $service_types_name,
//                    '$file_name',
//                    $file_name,
//                    '$request_file_path',
//                    $request_file_path,
//                    '$service_file_path',
//                    $service_file_path,
//                    Storage::disk('uploads')->exists($request_file_path),
//                    in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_type_name), true),
//                    '$request_icon_path',
//                    $request_icon_path,
//                    $service_icon_path,
//                    $service_types_file_path = substr($service_icon_path, 0, strrpos($service_icon_path, '/')),
//                    $service_types_icon_path,
//                    $service_types_file_path = substr($service_types_icon_path, 0, strrpos($service_types_icon_path, '/')),
//                    Storage::disk('uploads')->files($service_types_file_path),
//                    Storage::disk('uploads')->allFiles($service_types_file_path),
//                );

                $service_types_file_path = substr($service_types_icon_path, 0, strrpos($service_types_icon_path, '/'));
                if (empty($service_types_icon_path) === false):
                    Storage::disk('uploads')->deleteDirectory($service_types_file_path . '/');
                    Storage::disk('uploads')->delete($service_types_icon_path . '/');
                endif;
                $request_icon_path = $request->file('icon_path')->storeAs(
                    $request_service_types_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        else:
            Storage::disk('uploads')->delete($service_types_icon_path);
        endif;

        $service_types->update([
            'name' => $request_service_type_name,
            'description' => $request_service_type_description,
            'icon_path' => $request_icon_path,
            'order' => $request_service_types_order,
            'service_id' => $request_service_type_service_id,
        ]);

        $message = "$service_types_name service type updated to $request_service_type_name successfully>";

        return Response::json([
            'message' => $message,
            'service_types' => ServiceTypesResource::make($service_types),
//            'service' => ServiceResource::make($service),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $service_type = ServiceType::find($id);

        $msg = '';
        if ($service_type === null):
            $msg = 'Wrong Request ! Service Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $service_type_name = $service_type->name;
        $service_type_icon_path = $service_type->icon_path;

        $service_type_icon_path_array = explode('/', $service_type_icon_path);
        $base_service_type_icon_path = $service_type_icon_path_array[0] . '/';

//        dd(
//            $service_type_icon_path_array,
//            $base_service_type_icon_path,
//        );

        if (Storage::disk('uploads')->exists($service_type_icon_path) ||
            in_array($service_type_icon_path, Storage::disk('uploads')->allFiles($service_type_name), true)):
            Storage::disk('uploads')->deleteDirectory($base_service_type_icon_path);
            Storage::disk('uploads')->delete($service_type_icon_path);
        endif;

        $service_type->forceDelete();

        $message = "$service_type_name service type deleted successfully";
        return Response::json([
                'message' => $message
            ]
            , 200);

    }
}
