<?php

use App\Http\Controllers\Api\AuthTokensController;
use App\Http\Controllers\Api\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'api.',
    'middleware' => ['guest:sanctum'],
], function () {
    //Register user if user not exists && generate Token -- Register
    Route::post('register', [AuthTokensController::class, 'register'])->name('register');
    //Register Token For user if user - password exists -- Login
    Route::post('login', [AuthTokensController::class, 'login'])->name('login');

//    Route::post('forgot-password', [ForgotPasswordController::class, 'forgot_password'])->name('password.email');
    Route::post('forgot-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('password.email');
    Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('password.reset');
//    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
//    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.reset');

});

