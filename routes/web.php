<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:user'])->name('dashboard');


Route::name('dashboard.')->middleware(['auth', 'verified', 'role:[admin]'])->prefix('dashboard')->group(function () {
    Route::get('/admin', function () {
        return view('dashboard.dashboard');
    });
});

require __DIR__ . '/Dashboard/users.php';
require __DIR__ . '/Dashboard/services_types.php';
require __DIR__ . '/Dashboard/services.php';
require __DIR__ . '/Dashboard/products.php';
//require __DIR__.'/auth.php';
