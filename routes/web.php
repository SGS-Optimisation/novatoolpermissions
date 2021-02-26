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

        Route::group(['prefix' => '{clientAccount?}'], function(){

            Route::get('/', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
                ->name('home');

            Route::get('/dashboard', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
                ->name('dashboard');

            Route::get('/configuration',
                [\App\Http\Controllers\PMs\ClientAccountController::class, 'configuration'])
                ->name('configuration');

            /*
             * Rules section
             */
            Route::group(['prefix' => '/rules'], function(){

                Route::get('/', [\App\Http\Controllers\PMs\ClientAccountController::class, 'rules'])
                    ->name('rules');

                Route::get('/create', [\App\Http\Controllers\PMs\RuleController::class, 'create'])
                    ->name('rules.create');

                Route::post('/store', [\App\Http\Controllers\PMs\RuleController::class, 'store'])
                    ->name('rules.store');

                Route::get('/{id}/edit', [\App\Http\Controllers\PMs\RuleController::class, 'edit'])
                    ->name('rules.edit');

                Route::put('/{id}/update', [\App\Http\Controllers\PMs\RuleController::class, 'update'])
                    ->name('rules.update');

                Route::put('/{id}/taxonomy/update', [\App\Http\Controllers\PMs\TaxonomyController::class, 'update'])
                    ->name('rules.taxonomy.update');
            });

    });
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
