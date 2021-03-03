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
})->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(route('home'));
    })->name('dashboard');

    Route::put('/current-team', [\App\Http\Controllers\CurrentTeamController::class, 'update'])
        ->name('current-team.update');
});

Route::name('pm.')
    ->prefix('pm/')
    ->middleware([
        'auth:sanctum',
        'verified',
        'cache.headers:public;max_age=3600;etag',
    ])->group(function () {

        Route::get('/', [\App\Http\Controllers\PMs\HomeController::class, 'index'])
            ->name('landing');

        Route::name('client-account.')
            ->prefix('/{clientAccount:slug}')
            ->group(function () {

                Route::get('/', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index']);

                Route::get('/dashboard', [\App\Http\Controllers\PMs\ClientAccountController::class, 'index'])
                    ->name('dashboard');

                /*
                 * Configuration section
                 */
                Route::group(['prefix' => '/configuration'], function () {
                    Route::get('/',
                        [\App\Http\Controllers\PMs\ClientAccountTaxonomyController::class, 'show'])
                        ->name('configuration');

                    Route::put('/',
                        [\App\Http\Controllers\PMs\ClientAccountTaxonomyController::class, 'update'])
                        ->name('configuration.update');
                });

                /*
                 * Rules section
                 */
                Route::group(['prefix' => '/rules'], function () {

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

                    Route::put('/{id}/taxonomy/update',
                        [\App\Http\Controllers\PMs\RuleTaxonomyController::class, 'update'])
                        ->name('rules.taxonomy.update');
                });
            });

        Route::name('terms.')
            ->prefix('/terms')
            ->group(function () {
                Route::put('/{id}', [\App\Http\Controllers\PMs\TermController::class, 'update'])->name('update');

                Route::delete('/{id}', [\App\Http\Controllers\PMs\TermController::class, 'destroy'])->name('destroy');
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
