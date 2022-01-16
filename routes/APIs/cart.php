<?php

use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'role:[user]'],
], function () {

    Route::prefix('/')
        ->as('auth.')
        ->group(function () {

            Route::delete('cart', [CartController::class, 'clear_cart'])->name('cart.clear');
            Route::apiResource('cart', CartController::class);

        });
});
