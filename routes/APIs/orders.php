<?php


use App\Http\Controllers\Api\OrdersController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'role:[user]'],
], function () {

    Route::prefix('/orders/')
        ->as('orders.user.')
        ->group(function () {

            Route::get('current-orders', [OrdersController::class, 'current_orders'])->name('current_orders');
            Route::get('completed-orders', [OrdersController::class, 'completed_orders'])->name('completed_orders');
            Route::post('/', [OrdersController::class, 'store'])->name('store');
            Route::put('place-order', [OrdersController::class, 'place_order'])->name('place_order');

            //Route::get('orders/{id}', [OrdersController::class, 'show'])->name('api.orders.order_details');
            //Route::apiResource('orders', OrdersController::class)->names('api.orders');

        });
    Route::get('user/order/{id}', [OrdersController::class, 'user_show_order_details'])->name('user.order.order_details');
});
