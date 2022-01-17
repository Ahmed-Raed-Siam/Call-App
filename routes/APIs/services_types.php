<?php

use App\Http\Controllers\Api\ServiceTypesController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '/auth',
    'as' => 'api.auth.',
    'middleware' => ['auth:sanctum', 'role:[admin,user]'],
], function () {
    Route::prefix('/services/types')
        ->as('services.types.')
        ->group(function () {

            Route::get('/', [ServiceTypesController::class, 'index'])->name('index');
            Route::get('{type}', [ServiceTypesController::class, 'show'])->name('show');

            //Route::apiResource('services/types', ServiceTypesController::class)->names('api.services.types');
        });
});
