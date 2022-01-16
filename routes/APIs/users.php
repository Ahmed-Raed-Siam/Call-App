<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => '/auth',
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'role:[admin,user]'],
], function () {
    Route::prefix('/user')
        ->as('user.profile.')
        ->group(function () {

            Route::get('profile', [UserController::class, 'user_profile'])->name('profile');
            Route::put('profile', [UserController::class, 'update_user_profile_information'])->name('update');


            //Route::apiResource('users', UserController::class)->names('api.users');
        });
});


