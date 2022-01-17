<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdersResource;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Throwable;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function current_orders(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $user_orders = $user->orders()->with('products')->where('orders.status', '<>', 'completed')->latest()->get();
//        $user_order_products = $user->orderProducts()->get();

//        foreach ($user_orders->get() as $user_order_product):
//            dd(
//                $user_orders->latest()->get(),
//                $user_order_product,
//                '$user_order_product',
//                $user_order_product->products,
//            );
//        endforeach;

//        dd(
//            $user_orders,
////            $user_order_products,
////            $user_orders->first()->products()->get(),
//        );
        if (count($user_orders) > 0):
            return Response::json([
                'user_orders' => OrdersResource::collection($user_orders),
//            $user_cart_products,
            ], 200);
        endif;

        return Response::json([
            'message' => "you don't have any current orders",
//            $user_cart_products,
        ], 200);

    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function completed_orders(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        $user_orders = $user->orders()->with('products')->where('orders.status', '=', 'completed')->latest()->get();
//        $user_order_products = $user->orderProducts()->get();

//        foreach ($user_orders->get() as $user_order_product):
//            dd(
//                $user_orders->latest()->get(),
//                $user_order_product,
//                '$user_order_product',
//                $user_order_product->products,
//            );
//        endforeach;

//        dd(
//            $user_orders,
////            $user_order_products,
////            $user_orders->first()->products()->get(),
//        );
        if (count($user_orders) > 0):
            return Response::json([
                'user_orders' => OrdersResource::collection($user_orders),
//            $user_cart_products,
            ], 200);
        endif;

        return Response::json([
            'message' => "you don't have any completed orders",
//            $user_cart_products,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $user_orders = $user->orders();
//        dd(
//            $user_orders->latest()->get(),
//            'latest',
//            $user_orders->latest()->first(),
//            $user_orders->latest()->first()->status,
//        );
        $cart_products = $user->cartProducts();
        $message = 'No products in the cart. --Cart Empty!';
        $code = 500;
        if ($cart_products->count() > 0):

            $request->validate([
                'tax' => ['nullable', 'int', 'min:1', 'max:100'],
                'discount' => ['nullable', 'int', 'min:1', 'max:100'],
                'billing_address' => ['nullable', 'string', 'max:255'],
                'billing_city' => ['required', 'string', 'max:255'],
                'billing_neighborhood' => ['required', 'string', 'max:255'],
                'billing_street' => ['required', 'string', 'max:255'],
                'billing_building_number' => ['required', 'string', 'max:255'],
                'booking_date' => ['required', 'date'],
            ]);

            $request_tax = $request->post('tax', 0);
            $request_discount = $request->post('discount', 0);

            $request_billing_address = $request->post('billing_address');
            $request_billing_country = $request->post('billing_country');
            $request_billing_city = $request->post('billing_city');
            $request_billing_neighborhood = $request->post('billing_neighborhood');
            $request_billing_street = $request->post('billing_street');
            $request_billing_building_number = $request->post('billing_building_number');
            $request_booking_date = $request->post('booking_date');

            $total_price = 0;
            foreach ($cart_products->get() as $cart_item):
//            dd(
//                $cart_item->cart->price,
//            );
                $total_price += $cart_item->cart->price;
            endforeach;

            /*$order = Order::forceCreate([
                'user_id' => $user->id,
            ]);*/

            DB::beginTransaction();


//            $order_status = $order->status;
//
////            dd(
////                $order,
////                $order->number,
////                $order_status,
////                $order_status !== 'on-cart',
////            );
//
//            if ($order_status !== 'on-cart'):
//                foreach ($user->cartProducts as $product) {
//                    $order->products()->attach($product->id, [
//                        'quantity' => $product->cart->quantity,
//                        'price' => $product->cart->price,
//                    ]);
//                }
//            endif;

//            dd(
//                $user->orders()->get(),
//            );

//            dd(
//                $user_orders->latest()->get(),
////                $user_orders,
//                $user_orders->get()->count(),
////                $user_orders->latest()->first(),
////                $user_orders->latest()->first()->status,
//            );

            try {

                if ($user_orders->get()->count() === 0):
                    $order = $user_orders->create([
                        'user_id' => $user_id,
                        'status' => 'on-cart',
//                    'tax' => $request_tax,
//                    'discount' => $request_discount,
                        'billing_address' => $request_billing_address,
                        'billing_country' => $request_billing_country,
                        'billing_city' => $request_billing_city,
                        'billing_neighborhood' => $request_billing_neighborhood,
                        'billing_street' => $request_billing_street,
                        'billing_building_number' => $request_billing_building_number,
                        'booking_date' => $request_booking_date,
                        'total_cost' => $total_price,
                    ]);
                    $order_number = $order->number;
                    $message = "$order->name order created successfully. please make sure to complete the application to be confirmed!";
                    $code = 200;
                endif;

                if ($user_orders->get()->count() >= 0 && $user_orders->latest()->first()->status !== 'on-cart'):
                    $order = $user_orders->create([
                        'user_id' => $user_id,
                        'status' => 'on-cart',
//                    'tax' => $request_tax,
//                    'discount' => $request_discount,
                        'billing_address' => $request_billing_address,
                        'billing_country' => $request_billing_country,
                        'billing_city' => $request_billing_city,
                        'billing_neighborhood' => $request_billing_neighborhood,
                        'billing_street' => $request_billing_street,
                        'billing_building_number' => $request_billing_building_number,
                        'booking_date' => $request_booking_date,
                        'total_cost' => $total_price,
                    ]);

                    $order_number = $order->number;
//                $cart_products->update(['order_id' => $order_id]);

                    $message = "$order->name order created successfully. please make sure to complete the application to be confirmed!";
                    $code = 200;
                else:
                    $message = "you don't order anything! please confirm your order.";
                endif;

                DB::commit();


                //event(new NewOrder($order));

//            Auth::user()->notify(new NewOrderNotification($order));


                //event(new OrderCreated($order));

            } catch (Throwable $e) {
                DB::rollBack();
                return Response::json([
                    'message' => $e->getMessage(),
                ], 500);
            }

        endif;

        return Response::json([
            'message' => $message,
        ], $code);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user_order = Order::with('products')->where('id', '=', $id)->first();
//        dd($user_orders);

        if ($user_order === null):
            $message = 'Wrong Request ! Order Not Exists.';
            return Response::json([
                'message' => $message,
            ], 500);
        endif;

        return Response::json([
//            'order' => $user_order,
            'user_order' => OrdersResource::make($user_order),
        ], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function user_show_order_details(int $id): JsonResponse
    {

        $user = Auth::guard('sanctum')->user();
//        $user_orders = $user->orders()->with('products')->where('orders.status', '<>', 'completed')->latest()->get();
        $user_order = $user->orders()->with('products')->where('orders.id', '=', $id)->first();
//        dd($user_orders);

        if ($user_order === null):
            $message = 'Wrong Request ! Order Not Exists.';
            return Response::json([
                'message' => $message,
            ], 500);
        endif;

        return Response::json([
//            'order' => $user_order,
            'user_order' => OrdersResource::make($user_order),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return JsonResponse
     */
    public function place_order(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $user_id = $user->id;

        $user_orders = $user->orders();
        $cart_products = $user->cartProducts();
        $user_orders_latest = $user_orders->latest();

//        dd(
//            $user_orders,
//            $cart_products,
//            $user_orders_latest,
//        );
        if ($user_orders->count() > 0 && $cart_products->count() > 0 && $user_orders_latest->first()->status === 'on-cart'):
//        $order = $user_orders->latest()->first()->update(['status' => 'pending-payment']);

            $order = $user_orders_latest->first();
//            if ($order->status !== 'on-cart' && $cart_products->count() > 0):
//                $message = "please confirm your order!";
//                return Response::json([
//                    'message' => $message,
//                ], 500);
//            endif;

//            $order->status = 'pending-payment';
            $order->update(['status' => 'pending-payment']);

            $order_status = $order->status;

//            dd(
//                $order_status,
//                $user->cartProducts,
//                $user_orders->count() > 0,
//                $cart_products->count() > 0,
//                $user_orders->count() > 0 && $cart_products->count() > 0,
//                $user_orders_latest->first()->status === 'on-cart',
//            );

//            if ($order_status !== 'on-cart')
            foreach ($user->cartProducts as $product) {
                $order->products()->attach($product->id, [
                    'quantity' => $product->cart->quantity,
                    'price' => $product->cart->price,
                ]);
            }

            /*foreach ($user->cartProducts as $product) {
                //$user->cartProducts()->detach($product->id);
            }*/

            Cart::where('user_id', $user->id)->delete();

            $message = "$order->number order created successfully>";
            return Response::json([
                'message' => $message,
            ], 200);

//        endif;

        endif;

//        dd(
//            $user_orders->get(),
//            $order,
//            $order_status,
//            $user->cartProducts,
//        );

        $message = "you don't have any element in the cart or you don't have any order!";
        return Response::json([
            'message' => $message,
        ], 500);
    }


    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user_order = Order::with('products')->where('id', '=', $id)->first();
//        dd($user_orders);

        if ($user_order === null):
            $message = 'Wrong Request ! Order Not Exists.';
            return Response::json([
                'message' => $message,
            ], 500);
        endif;

        $request->validate([
            'status' => [
                'required',
                'string',
                'max:255',
                Rule::in([
//                    'pending-payment',
                    'payment-completed',
                    'canceled',
                    'completed',
                ]),
            ],
        ]);

        $order_status = $request->post('status');

//        dd(
//            $request->all(),
//            $order_status,
//        );

        $user_order->update(['status' => $order_status]);


        return Response::json([
//            'order' => $user_order,
            'user_order' => OrdersResource::make($user_order),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
