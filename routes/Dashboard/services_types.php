<?php

use App\Http\Controllers\Dashboard\ServiceTypesController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '/dashboard',
    'as' => 'dashboard.',
    'middleware' => ['auth', 'role:[admin]'],
], function () {
    Route::prefix('/admin/services')->
        as('services.')
        ->group(function () {
            Route::resource('types', ServiceTypesController::class);
        });
});
