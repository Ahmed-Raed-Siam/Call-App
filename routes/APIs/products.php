<?php

use App\Http\Controllers\Api\ProductsController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '/auth',
    'as' => 'api.auth.',
    'middleware' => ['auth:sanctum', 'role:[admin,user]'],
], function () {
    Route::prefix('/products')
        ->as('products.')
        ->group(function () {
            //Route::post('products/trash/{trash}', [ProductsTrashController::class, 'restore'])->name('trash.restore');
            //Route::apiResource('products/trash', ProductsTrashController::class)->names('trash');

            Route::get('/', [ProductsController::class, 'index'])->name('index');
            Route::get('{id}', [ProductsController::class, 'show'])->name('show');

            //Route::apiResource('products', ProductsController::class)->names('api.products');
        });
});
