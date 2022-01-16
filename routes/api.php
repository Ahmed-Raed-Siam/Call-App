<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('auth/user', function (Request $request) {
    return \Illuminate\Support\Facades\Response::json([
        'user' => \App\Http\Resources\UserResource::make($request->user()),
    ], 200);
})->name('api.user');

////Register user if user not exists && generate Token -- Register
//Route::middleware('guest:sanctum')->post('auth/register', [AuthTokensController::class, 'register'])->name('api.register');
////Register Token For user if user - password exists -- Login
//Route::middleware('guest:sanctum')->post('auth/login', [AuthTokensController::class, 'login'])->name('api.login');
//
//Route::middleware('guest:sanctum')->post('forgot-password', [ForgotPasswordController::class, 'forgot_password'])->name('api.password.email');
////Route::middleware('guest:sanctum')->post('forgot-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('api.password.email');
////Route::middleware('guest:sanctum')->get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
//Route::middleware('guest:sanctum')->post('reset-password', [ForgotPasswordController::class, 'reset'])->name('api.password.reset');


require __DIR__ . '/APIs/guest/guest.php';
require __DIR__ . '/APIs/tokens.php';
require __DIR__ . '/APIs/users.php';
require __DIR__ . '/APIs/services_types.php';
require __DIR__ . '/APIs/services.php';
require __DIR__ . '/APIs/products.php';
require __DIR__ . '/APIs/cart.php';
require __DIR__ . '/APIs/orders.php';
require __DIR__ . '/APIs/diet.php';
require __DIR__ . '/APIs/website_info.php';
//require __DIR__ . '/APIs/physical_activity.php';
