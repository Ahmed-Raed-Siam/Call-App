<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollectionResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ProductsTrashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::onlyTrashed()->orderBy('order', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate();
        return Response::json(new ProductCollectionResource($products), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->with('service_type')->find($id);
        $msg = '';

        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists in the trash.';
            return Response::json([
                'msg' => $msg,
            ], 200);
        endif;

//        $service_type = $product->service_type;
        return Response::json([
            'product' => ProductResource::make($product),
//            'service_type' => $service_type,
//            'count_service_types' => count($product),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->find($id);

        $msg = '';
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $product_name = $product->name;
        $product_image = $product->image;

        $product_image_array = explode('/', $product_image);
        $base_product_image = $product_image_array[0] . '/';

//        dd(
//            $product_image_array,
//            $base_service_type_image,
//        );

        if (Storage::disk('uploads')->exists($product_image) ||
            in_array($product_image, Storage::disk('uploads')->allFiles($product_name), true)):
            Storage::disk('uploads')->deleteDirectory($base_product_image);
            Storage::disk('uploads')->delete($product_image);
        endif;

        $product->forceDelete();

        $message = "$product_name product deleted from the trash successfully>";
        return Response::json([
                'message' => $message
            ]
            , 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function restore(int $id): JsonResponse
    {
        $product = Product::onlyTrashed()->find($id);

        $msg = '';
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $product_name = $product->name;

        $product->restore();

        $message = "You restore $product_name product from the trash successfully>";
        return Response::json([
                'message' => $message
            ]
            , 200);

    }

}
