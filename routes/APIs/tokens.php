<?php

use App\Http\Controllers\Api\AuthTokensController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'role:[user]'],
], function () {
    Route::prefix('/tokens')
        ->as('tokens.')
        ->group(function () {
            Route::get('/', [AuthTokensController::class, 'index'])->name('index');
            Route::delete('logout', [AuthTokensController::class, 'current_logout'])->name('current_logout');
//            Route::delete('{id}', [AuthTokensController::class, 'destroy'])->name('destroy');
            Route::delete('/', [AuthTokensController::class, 'logout_all'])->name('current_logout_all');
        });
});
