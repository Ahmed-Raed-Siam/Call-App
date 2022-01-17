<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $products = Product::withoutTrashed()->orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return response()->view('dashboard.products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $service_types = ServiceType::all();

        return response()->view('dashboard.products.create', [
            'service_types' => $service_types,
        ]);
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
            'name' => ['required', 'string', 'max:255', 'unique:products,name'],
//            'name' => ['required', 'string', 'max:255',
//                Rule::unique('products', 'name'),
//            ],
            'description' => ['required', 'string', 'unique:products,description'],
            'image.*' => ['nullable', 'image', 'mimes:jpg,bmp,png', 'dimensions:min_width=200,min_height=200'],
            'service_type_id' => ['required', 'string', 'numeric', 'exists:service_types,id'],
            'order' => ['nullable', 'numeric'],
            'cost' => ['required', 'numeric'],
        ]);

        $request_product_name = $request->post('name');
        $request_product_description = $request->post('description');
        $request_service_type_id = $request->post('service_type_id');
        $request_product_cost = (float)$request->post('cost');

        $service_type = ServiceType::findOrfail($request_service_type_id);

        if ($request->post('order')):
            $request_product_order = (int)$request->post('order');
        else:
            $request_product_order = 0;
        endif;

        $product_image = '';
        $request_new_product_name = str_replace(' ', '_', $request_product_name);

        if ($request->hasFile('image')):
            $file_name = $request->file('image')->getClientOriginalName();// file name with extension
            $request_file_path = $request_new_product_name . '/' . $file_name;

            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_new_product_name), true)):
                $product_image = $request_file_path;
            else:
                $product_image = $request->file('image')->storeAs(
                    $request_new_product_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        endif;

        $product = $service_type->products()->create([
            'name' => $request_product_name,
            'description' => $request_product_description,
            'image' => $product_image,
            'service_id' => $request_service_type_id,
            'order' => $request_product_order,
            'cost' => $request_product_cost,
        ]);

        $alert_status = 'alert-success';
        // Msg
        $msg = 'New Product Added Successfully.';
        // Pref
        $pref = "You Add $request_product_name As New Product To $service_type->name Service Type To The System!<br>His ID : $product->id ,His Description : $request_product_description ,His Order Queue : $request_product_description . ";
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
        $product = Product::withoutTrashed()->with('service_type')->findOrfail($id);

        return response()->view('dashboard.products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $product = Product::withoutTrashed()->with('service_type')->findOrfail($id);
        $service_types = ServiceType::all();

        return response()->view('dashboard.products.edit', [
            'product' => $product,
            'service_types' => $service_types,
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

        $product = Product::withoutTrashed()->with('service_type')->findOrfail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'description' => [
                'required',
                'string',
                Rule::unique('products')->ignore($product->id),
            ],
            'image.*' => [
                'nullable',
                'image',
                'mimes:jpg,bmp,png',
                'dimensions:min_width=200,min_height=200',
            ],
            'service_type_id' => ['required', 'string', 'numeric', 'exists:service_types,id'],
            'order' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:1'],
        ]);

        $product_name = $product->name;
        $product_order = $product->order;
        $product_image_path = $product->image;

        if ($request->post('order')):
            $request_product_order = (int)$request->post('order');
        else:
            $request_product_order = $product_order;
        endif;

        $request_product_name = $request->post('name');
        $request_product_description = $request->post('description');
        $request_service_type_id = $request->post('service_type_id');
        $request_product_cost = (float)$request->post('cost');

        $request_product_image_path = '';
        $request_new_product_name = str_replace(' ', '_', $request_product_name);

        if ($request->hasFile('image')):
            $file_name = $request->file('image')->getClientOriginalName();// file name with extension
            $request_file_path = $request_new_product_name . '/' . $file_name;
            if ($request_file_path !== $product_image_path):
                $request->validate([
                    'image.*' => [
                        'nullable',
                        'image',
                        'mimes:jpg,bmp,png',
                        'dimensions:min_width=200,min_height=200',
                        Rule::unique('products')->ignore($product->id)
                    ],
                ]);
//                dd('sasaas');

            endif;

            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_new_product_name), false)):
                $request_product_image_path = $request_file_path;
            else:
//                dd(
//                    'exists',
//                    $product_image_path,
//                    is_null($product_image_path) === false,
//                );
                if (is_null($product_image_path) === false):
                    $product_file_path = substr($product_image_path, 0, strrpos($product_image_path, '/'));
                    Storage::disk('uploads')->deleteDirectory($product_file_path . '/');
                    Storage::disk('uploads')->delete($product_image_path . '/');
                endif;

                $request_product_image_path = $request->file('image')->storeAs(
                    $request_new_product_name, $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        else:
            Storage::disk('uploads')->delete($product_image_path);
        endif;

        $product->update([
            'name' => $request_product_name,
            'description' => $request_product_description,
            'image' => $request_product_image_path,
            'service_id' => $request_service_type_id,
            'order' => $request_product_order,
            'cost' => $request_product_cost,
        ]);

        $alert_status = 'alert-success';
        // Msg
        $msg = "Edit Product $product_name Successfully.";
        // Pref
        $pref = "You Edit $product_name to $request_product_name Product in The System!<br>His ID : $id ,His Description : $request_product_description ,His Order Queue : $request_product_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.products.index')->with('status', $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $product = Product::withoutTrashed()->findOrfail($id);

        $product_name = $product->name;
        $product_image = $product->image;

        $product->delete();

        // Status for Deleting This Service from The System!
        $alert_status = 'alert-warning';
        // Msg
        $msg = "$product_name product deleted successfully.";
        // Pref
        $pref = "You Delete $product_name Product from The System!<br>His ID : $id , <br> His Description : $product->description . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.products.index')->with('status', $status);
    }
}
