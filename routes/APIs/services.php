<?php


use App\Http\Controllers\Api\ServicesController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.auth.',
    'middleware' => ['auth:sanctum', 'role:[admin,user]'],
], function () {

    Route::prefix('/services')
        ->as('services.')
        ->group(function () {

            Route::get('/', [ServicesController::class, 'index'])->name('index');
            Route::get('{service}', [ServicesController::class, 'show'])->name('show');

            //Route::apiResource('services', ServicesController::class)->names('api.services');
        });
});

