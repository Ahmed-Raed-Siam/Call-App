<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $services = Service::orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return response()->view('dashboard.services.index', ['services' => $services]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return response()->view('dashboard.services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'unique:services,name'],
            'description' => ['required', 'string', 'unique:services,description'],
            'icon_path' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'order' => ['nullable', 'numeric'],
        ]);

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

            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($new_service_name))):
                $icon_path = $request_file_path;
            else:

                $icon_path = $request->file('icon_path')->storeAs(
                    $service_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        endif;


        $service = Service::create([
            'name' => $service_name,
            'description' => $service_description,
            'icon_path' => $icon_path,
            'order' => $service_order,
        ]);

        $alert_status = 'alert-success';
        // Msg
        $msg = 'New Service Added Successfully.';
        // Pref
        $pref = "You Add $service_name As New Service To The System!<br>Her ID : $service->id ,Her Description : $service_description ,Her Order Queue : $service_order . ";
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
        $service = Service::findOrfail($id);

        $service_types = $service->service_types()->get();
        $count_service_types = count($service_types);

        if (empty($service_types) || $count_service_types === 0):
            $service_types = 'no service type to this service';
        endif;

        return response()->view('dashboard.services.show', [
                'service' => $service,
                'service_types' => $service_types,
                'count_service_types' => $count_service_types]
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
        $service = Service::findOrfail($id);
        return response()->view('dashboard.services.edit', [
            'service' => $service,
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
        $service = Service::findOrfail($id);

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
        $service_icon_path_model = $service->icon_path;
        $service_order_model = $service->order;

        if ($request->post('order')):
            $request_service_order = (int)$request->post('order');
        else:
            $request_service_order = $service_order_model;
        endif;

        $request_service_name = $request->post('name');
        $request_service_description = $request->post('description');

        $new_request_service_name = str_replace(' ', '_', $request_service_name);

        if ($request->hasFile('icon_path')):
            $file_name = $request->file('icon_path')->getClientOriginalName();// file name with extension
            $request_file_path = $new_request_service_name . '/' . $file_name;
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
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($new_request_service_name), false)):
                $request_icon_path = $request_file_path;
            else:
                $service_types_file_path = substr($service_icon_path_model, 0, strrpos($service_icon_path_model, '/'));
                if (empty($service_icon_path_model) === false):
                    Storage::disk('uploads')->deleteDirectory($service_types_file_path . '/');
                    Storage::disk('uploads')->delete($service_icon_path_model . '/');
                endif;
                $request_icon_path = $request->file('icon_path')->storeAs(
                    $new_request_service_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
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

//        dd(
//            $request->all(),
//            $service,
//            $request_icon_path,
//            $request_file_path,
//        );

        $alert_status = 'alert-success';
        // Msg
        $msg = "Edit Service $service_name_model Successfully.";
        // Pref
        $pref = "You Edit $service_name_model to $request_service_name Service in The System!<br>Her ID : $id ,Her Description : $request_service_description ,Her Order Queue : $request_service_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.services.index')->with('status', $status);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $service = Service::findOrfail($id);

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
            $error_msg = "You can't delete this service because it related to {$service_types->count()} service types.\n Are $deleted_files .";
            abort(403, $error_msg);
        endif;

        $service->forceDelete();

        // Status for Deleting This Service from The System!
        $alert_status = 'alert-warning';
        // Msg
        $msg = "$service_name service deleted with all service types successfully.";
        // Pref
        $pref = "You Delete $service_name Service from The System!<br>Her ID : $id , <br> Her Description : $service->description . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.services.index')->with('status', $status);
    }
}
