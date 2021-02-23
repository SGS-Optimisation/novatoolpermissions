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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    //return Inertia\Inertia::render('Dashboard');
    return redirect(route('home'));
})->name('dashboard');

Route::group([
    'middleware' => [
        'auth:sanctum',
        'verified',
        'cache.headers:public;max_age=3600;etag',
    ],
    'prefix' => 'pm/'
],
    function () {

        Route::get('/{clientAccount?}', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
            ->name('home');

        Route::get('/{clientAccount?}/dashboard', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
            ->name('dashboard');

        Route::get('/{clientAccount}/rules', [\App\Http\Controllers\PMs\ClientAccountController::class, 'rules'])
            ->name('rules');

        Route::get('/{clientAccount}/rules/create', [\App\Http\Controllers\PMs\RuleController::class, 'create'])
            ->name('rules.create');

        Route::post('/{clientAccount}/rules/store', [\App\Http\Controllers\PMs\RuleController::class, 'store'])
            ->name('rules.store');

        Route::get('/{clientAccount}/rules/{id}/edit', [\App\Http\Controllers\PMs\RuleController::class, 'edit'])
            ->name('rules.edit');

        Route::put('/{clientAccount}/rules/{id}/update', [\App\Http\Controllers\PMs\RuleController::class, 'update'])
            ->name('rules.update');




        Route::get('/{clientAccount}/configuration',
            [\App\Http\Controllers\PMs\ClientAccountController::class, 'configuration'])
            ->name('configuration');
    });


Route::group([
    'middleware' => [
        'auth:sanctum',
        'verified',
        'cache.headers:public;max_age=3600;etag',
    ],
    'prefix' => 'op/'
],
    function () {
        Route::get('/{jobNumber?}', [\App\Http\Controllers\OPs\JobController::class, 'index'])
            ->name('op.home');
    });
