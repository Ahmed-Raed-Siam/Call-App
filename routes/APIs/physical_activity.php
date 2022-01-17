<?php

use App\Http\Controllers\Api\PhysicalActivitiesController;
use App\Http\Controllers\Api\WebsiteInfoController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/auth',
    'as' => 'api.auth.',
    'middleware' => ['auth:sanctum', 'role:[admin,user]'],
], function () {
    Route::prefix('/physical-activities')
        ->as('physical_activities.')
        ->group(function () {
            Route::get('/', [PhysicalActivitiesController::class, 'index'])->name('index');
            Route::get('/paginate', [PhysicalActivitiesController::class, 'paginate_index'])->name('paginate_index');
        });
});
