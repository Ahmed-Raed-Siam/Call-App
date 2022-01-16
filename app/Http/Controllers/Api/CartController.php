<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
//        $user_cart = $user->cart()->get();
        $cart_products = $user->cartProducts()->get();

//        dd(count($cart_products));

        if (count($cart_products) > 0):
            $total_price = 0;
            foreach ($cart_products as $cart_item):
//            dd(
//                $cart_item->cart->price,
//            );
                $total_price += $cart_item->cart->price;
            endforeach;

//        $ids = array_keys($product_ids);
//        $products = Product::whereIn('id', $ids)->get();

//        dd(
////            'user_cart',
////            $user_cart,
//            'cart_products',
//            $cart_products,
//            'first',
//            $cart_products->first(),
//            'first->cart',
//            $cart_products->first()->cart,
//        );

            return Response::json([
//            'user_cart' => $user_cart,
                'cart_products' => $cart_products,
//                'first->cart' => $cart_products->first()->cart,
//                'first->cart->price' => $cart_products->first()->cart->price,
                'total_price' => $total_price,
            ], 200);

        endif;

        $message = 'No products in the cart. --Cart Empty!';
        return Response::json([
            'message' => $message,
        ], 500);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public
    function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $request->validate([
            'product_id' => ['required', 'int', 'exists:products,id'],
            'quantity' => ['int', 'min:1', 'max:10'],
        ]);

        $request_product_id = $request->post('product_id');
        $request_product_quantity = $request->post('quantity', 1);
        $product = Product::withoutTrashed()->find($request_product_id);
        $product_price = $product->cost;

        $cart = DB::table('carts')->where([
            'user_id' => $user_id,
            'product_id' => $request_product_id,
        ]);

//        $cart = Cart::where([
//            'user_id' => $user_id,
//            'product_id' => $request_product_id,
//        ])->first();

//        dd(
//            'out',
//            $cart->first(),
////            '$cart1',
////            $cart1,
//        );

        $message = '';
        if (is_null($cart->first()) === false):
//            $old_quantity = $cart->first()->quantity;
            $cart->update([
                'quantity' => $request_product_quantity,
                'price' => $request_product_quantity * $product_price,
            ]);
            $message = "$product->name product updated successfully+>";
//            dd(
//                'aleardy',
//                $cart->first(),
//                $cart->first()->quantity,
//            );
        else:
            $message = "New $product->name product added to the cart+>";
            Cart::create([
                'user_id' => $user_id,
                'product_id' => $request_product_id,
                'quantity' => $request_product_quantity,
                'price' => $product_price,
            ]);
        endif;

        $total_price = 0;
        $user_orders = $user->orders();
        $cart_products = $user->cartProducts();
        $user_orders_latest = $user->orders()->latest();
        if ($user_orders->count() > 0 && $cart_products->count() > 0 && $user_orders_latest->first()->status === 'on-cart'):
            foreach ($cart_products->get() as $cart_item):
                $total_price += $cart_item->cart->price;
            endforeach;

            $order = $user_orders_latest->first();
            $order->update(['total_cost' => $total_price]);

        endif;

//        dd(
//            'Current User Cart',
//            $cart->first(),
//            $user->cart()->get(),
//        );

        return Response::json([
            'message' => $message
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public
    function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $product = Product::withoutTrashed()->find($id);

        $msg = '';
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $request->validate([
            'quantity' => ['int', 'min:-1', 'max:1'],
        ]);

        $product_id = $product->id;
        $request_product_quantity = (int)$request->post('quantity', 1);
//        $product = Product::find($request_product_id);
//        $product_price = $product->cost;

        $cart = DB::table('carts')->where([
            'user_id' => $user_id,
            'product_id' => $product_id,
        ]);

        $product_price = $product->cost;

        $message = "$product->name product updated successfully+>";
        if (is_null($cart->first()) === false):
            $old_quantity = $cart->first()->quantity;
            $new_quantity = $old_quantity + $request_product_quantity;
            /*New*/
            if ($new_quantity >= 1):
                $cart->update([
                    'quantity' => $new_quantity,
                    'price' => $new_quantity * $product_price,
                ]);
            endif;
//            dd(
//                'aleardy',
//                $cart->first(),
//                $cart->first()->quantity,
//            );
        else:
            $message = 'This product not in the Cart';
        endif;

        $total_price = 0;
        $user_orders = $user->orders();
        $cart_products = $user->cartProducts();
        $user_orders_latest = $user->orders()->latest();
        if ($user_orders->count() > 0 && $cart_products->count() > 0 && $user_orders_latest->first()->status === 'on-cart'):
            foreach ($cart_products->get() as $cart_item):
                $total_price += $cart_item->cart->price;
            endforeach;

            $order = $user_orders_latest->first();
            $order->update(['total_cost' => $total_price]);

        endif;

        return Response::json([
            'message' => $message,
//            'cart' => $cart->first(),
//            'old_quantity' => $old_quantity,
//            'request_product_quantity' => $request_product_quantity,
        ], 200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public
    function destroy(int $id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $product = Product::withoutTrashed()->find($id);

        $msg = '';
        if ($product === null):
            $msg = 'Wrong Request ! Product Not Exists.';
            return Response::json([
                'message' => $msg,
            ], 200);
        endif;

        $product_id = $product->id;
        $product_name = $product->name;

        $cart = DB::table('carts')->where([
            'user_id' => $user_id,
            'product_id' => $product_id,
        ]);

        $message = 'This product not in the Cart';
        if (is_null($cart->first()) === false):
            $message = "$product_name product deleted from the cart successfully>";
            $cart->delete();
        endif;

        $total_price = 0;
        $user_orders = $user->orders();
        $cart_products = $user->cartProducts();
        $user_orders_latest = $user->orders()->latest();
        if ($user_orders->count() > 0 && $cart_products->count() > 0 && $user_orders_latest->first()->status === 'on-cart'):
            foreach ($cart_products->get() as $cart_item):
                $total_price += $cart_item->cart->price;
            endforeach;

            $order = $user_orders_latest->first();
            $order->update(['total_cost' => $total_price]);

        endif;

        return Response::json([
            'message' => $message
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public
    function clear_cart(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $user_cart = $user->cart();
        if ($user_cart->count() > 0):
            $user_cart->forceDelete();
        endif;

        $user_orders = $user->orders();
        $cart_products = $user->cartProducts();
        $user_orders_latest = $user->orders()->latest();
        if ($user_orders->count() > 0 && $cart_products->count() >= 0 && $user_orders_latest->first()->status === 'on-cart'):
            $order = $user_orders_latest->first();
            $order->update(['total_cost' => 0]);
        endif;

        $message = "Cart cleared";
        return Response::json([
            'message' => $message
        ], 200);

    }
}
