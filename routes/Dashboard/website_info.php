<?php

use App\Http\Controllers\Api\WebsiteInfoController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.',
    'middleware' => ['auth:sanctum', 'role:[admin,user]'],
], function () {
    Route::prefix('/website-info')
        ->as('website_info.')
        ->group(function () {
            Route::post('/', [WebsiteInfoController::class, 'store'])->name('store');
            Route::get('/', [WebsiteInfoController::class, 'get_website_info'])->name('show');
        });
});
