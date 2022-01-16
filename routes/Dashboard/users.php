<?php

use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '/dashboard',
    'as' => 'dashboard.',
    'middleware' => ['auth', 'role:[admin]'],
], function () {
    Route::prefix('/admin')
        ->group(function () {
            Route::resource('users', UserController::class);
        });
});


