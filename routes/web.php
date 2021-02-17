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

/*Route::get('/', function () {
    return view('welcome');
});*/

/*Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');*/

Route::group(['middelware' => ['auth:sanctum', 'verified']], function(){

    Route::get('/{clientAccount?}', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
        ->name('home');

    Route::get('/{clientAccount?}/dashboard', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
        ->name('dashboard');

    Route::get('/{clientAccount}/rules', [\App\Http\Controllers\PMs\ClientAccountController::class, 'rules'])
        ->name('rules');

    Route::get('/{clientAccount}/configuration', [\App\Http\Controllers\PMs\ClientAccountController::class, 'configuration'])
        ->name('configuration');
});

