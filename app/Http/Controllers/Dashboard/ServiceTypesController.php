<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $service_types = ServiceType::orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return response()->view('dashboard.service_types.index', ['service_types' => $service_types]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $services = Service::all();
        return response()->view('dashboard.service_types.create', [
            'services' => $services,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
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
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_type_name), true)):
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

        $message = "New service type added to $service_type->name successfully>";

        $alert_status = 'alert-success';
        // Msg
        $msg = 'New Service Type Added Successfully.';
        // Pref
        $pref = "You Add $request_service_type_name As New Service Type To $service_name Service To The System!<br>Her ID : $service_type->id ,Her Description : $request_service_type_description ,Her Order Queue : $request_service_type_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->back()->with('status', $status);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $service_type = ServiceType::findOrfail($id);

        $service_type_products = $service_type->products()->get();
        $count_service_type_products = count($service_type_products);

        if (empty($service_type_products) || $count_service_type_products === 0):
            $service_type_products = 'no products to this service type!';
        endif;

        return response()->view('dashboard.service_types.show', [
                'service_type' => $service_type,
                'service_type_products' => $service_type_products,
                'count_service_type_products' => $count_service_type_products]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $service_type = ServiceType::findOrfail($id);
        $services = Service::all();
        return response()->view('dashboard.service_types.edit', [
            'service_type' => $service_type,
            'services' => $services,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $service_types = ServiceType::findOrfail($id);

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

            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_types_name), false)):
                $request_icon_path = $request_file_path;
            else:

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

        $alert_status = 'alert-success';
        // Msg
        $msg = "Edit Service Type $service_types_name Successfully.";
        // Pref
        $pref = "You Edit $service_types_name to $request_service_type_name Service Type in The System!<br>Her ID : $id ,Her Description : $request_service_type_description ,Her Order Queue : $request_service_types_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.services.types.index')->with('status', $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $service_type = ServiceType::findOrfail($id);

        $service_type_name = $service_type->name;
        $service_type_icon_path = $service_type->icon_path;

        $service_type_icon_path_array = explode('/', $service_type_icon_path);
        $base_service_type_icon_path = $service_type_icon_path_array[0] . '/';

        if (Storage::disk('uploads')->exists($service_type_icon_path) ||
            in_array($service_type_icon_path, Storage::disk('uploads')->allFiles($service_type_name), true)):
            Storage::disk('uploads')->deleteDirectory($base_service_type_icon_path);
            Storage::disk('uploads')->delete($service_type_icon_path);
        endif;

        $service_type_products = $service_type->products();

        if ($service_type_products->count() > 0):
            $deleted_files = $service_type_products->get()->implode('name', ' , ');
            $error_msg = "You can't delete this service type because it related to {$service_type_products->count()} many products.\n Are $deleted_files .";
            abort(403, $error_msg);
        endif;

        $service_type->forceDelete();

        // Status for Deleting This Service from The System!
        $alert_status = 'alert-warning';
        // Msg
        $msg = "$service_type_name service deleted with all service types successfully.";
        // Pref
        $pref = "You Delete $service_type_name Service Type from The System!<br>Her ID : $id , <br> Her Description : $service_type->description . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.services.types.index')->with('status', $status);
    }
}
