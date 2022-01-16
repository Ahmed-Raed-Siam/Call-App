<?php

use App\Http\Controllers\Api\DietController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'role:[user]'],
], function () {

    Route::prefix('/dits/')
        ->as('user.diet.')
        ->group(function () {

            Route::get('current-diets', [DietController::class, 'current_diets'])->name('current_diets');
            Route::get('completed-diets', [DietController::class, 'completed_diets'])->name('completed_diets');
            Route::post('/', [DietController::class, 'store'])->name('store');
//            Route::put('place-order', [DietController::class, 'place_order'])->name('place_order');

        });
});
