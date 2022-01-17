<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::withoutTrashed()->orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate();
//        return Response::json(ServiceResource::collection($services), 200);
        return Response::json(new ProductCollectionResource($products), 200);
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

        $service_type = ServiceType::find($request_service_type_id);
        $msg = '';
        if ($service_type === null):
            $msg = 'Wrong Request ! Service Type Not Exists.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;

        if ($request->post('order')):
            $request_product_order = (int)$request->post('order');
        else:
            $request_product_order = 0;
        endif;

//        if ($request->post('cost')):
//            $request_product_cost = (float)$request->post('cost');
//        else:
//            $request_product_cost = 0;
//        endif;

        $product_image = '';
        $request_new_product_name = str_replace(' ', '_', $request_product_name);

//        dd(
//            $request->all(),
//            $service_type,
//            $request->hasFile('image'),
//            $request->file('image')->getClientOriginalName(),
//        );

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

//        $product = Product::create($request->except('image'));

//        if ($request->hasFile('image')) {
//            $cover = '';
//            foreach ($request->file('image') as $image) {
//
//                $path = $image->store('images', 'public');
//                if (empty($cover)) {
//                    $cover = $path;
//                }
//                /*ProductImage::create([
//                    'product_id' => $product->id,
//                    'image' => $path,
//                ]);*/
//                $product->images()->create([
//                    'image' => $path,
//                ]);
//            }
//
//            $product->update([
//                'image' => $cover,
//            ]);
//        }

        $product = $service_type->products()->create([
            'name' => $request_product_name,
            'description' => $request_product_description,
            'image' => $product_image,
            'service_id' => $request_service_type_id,
            'order' => $request_product_order,
            'cost' => $request_product_cost,
        ]);

        $message = "New product added to $service_type->name successfully>";
        return Response::json([
            'message' => $message,
            'product' => ProductResource::make($product),
//            'service' => ServiceResource::make($service_type),
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::withoutTrashed()->with('service_type')->find($id);
        $msg = '';
//        dd(
//            $product,
//        );
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;

//        $service_type = $product->service_type;
        return Response::json([
            'product' => ProductResource::make($product),
//            'service_type' => $service_type,
//            'count_service_types' => count($product),
//            'service' => ServiceResource::make($service),
//            'service' => $service,
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

        $product = Product::withoutTrashed()->find($id);

        $msg = '';
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

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
            'order' => ['nullable', 'numeric'],
            'cost' => ['required', 'numeric'],
        ]);


//        $service_type = $product->service_type;
//        $service_type_name = $service_type->name;
//        $service_type_icon_path = $service_type->icon_path;

//        dd(
//            $request->all(),
//            $product,
//            $service_type,
//        );

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
            endif;
//            dd(
//                $service_icon_path,
//                $service_file_path,
//                $service_file_path . '/' . $request_service_types_name . '/' . $file_name,
//                $request_service_type_name,
//                '$product_name',
//                $product_name,
//                '$file_name',
//                $file_name,
//                '$request_file_path',
//                $request_file_path,
//                'Storage--',
//                Storage::disk('uploads')->allFiles($request_service_type_name),
//                'Storage',
//                Storage::disk('uploads')->exists($request_file_path),
//                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_service_type_name), true),
//                '$request_product_image_path',
//                $request_product_image_path,
//            );
            if (Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($request_new_product_name), false)):
                $request_product_image_path = $request_file_path;
            else:
                $product_file_path = substr($product_image_path, 0, strrpos($product_image_path, '/'));
                Storage::disk('uploads')->deleteDirectory($product_file_path . '/');
                Storage::disk('uploads')->delete($product_image_path . '/');
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

        $message = "$product_name service type updated to $request_product_name successfully>";

        return Response::json([
            'message' => $message,
            'product' => ProductResource::make($product),
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
        $product = Product::withoutTrashed()->find($id);

        $msg = '';
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $product_name = $product->name;
        $product_image = $product->image;

        $product->delete();

        $message = "$product_name product deleted successfully>";
        return Response::json([
                'message' => $message
            ]
            , 200);
    }
}
