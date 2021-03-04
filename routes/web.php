<?php

use App\Http\Controllers\CurrentTeamController;
use App\Http\Controllers\PMs\ClientAccountController;
use App\Http\Controllers\PMs\ClientAccountTaxonomyController;
use App\Http\Controllers\PMs\HomeController;
use App\Http\Controllers\PMs\RuleController;
use App\Http\Controllers\PMs\RuleTaxonomyController;
use App\Http\Controllers\PMs\TaxonomyController;
use App\Http\Controllers\PMs\TermController;
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
})->name('home');*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(route('home'));
    })->name('dashboard');

    Route::put('/current-team', [CurrentTeamController::class, 'update'])
        ->name('current-team.update');
});

Route::name('pm.')
    ->prefix('pm/')
    ->middleware([
        'auth:sanctum',
        'verified',
        'cache.headers:public;max_age=3600;etag',
    ])->group(function () {

        Route::get('/', [HomeController::class, 'index'])
            ->name('landing');

        Route::name('client-account.')
            ->prefix('/{clientAccount:slug}')
            ->group(function () {

                Route::get('/', [ClientAccountController::class, 'index']);

                Route::get('/dashboard', [ClientAccountController::class, 'index'])
                    ->name('dashboard');

                /*
                 * Configuration section
                 */
                Route::group(['prefix' => '/configuration'], function () {
                    Route::get('/', [ClientAccountTaxonomyController::class, 'show'])
                        ->name('configuration');

                    Route::put('/', [ClientAccountTaxonomyController::class, 'update'])
                        ->name('configuration.update');
                });

                /*
                 * Rules section
                 */
                Route::group(['prefix' => '/rules'], function () {

                    Route::get('/', [ClientAccountController::class, 'rules'])
                        ->name('rules');

                    Route::get('/create', [RuleController::class, 'create'])
                        ->name('rules.create');

                    Route::post('/store', [RuleController::class, 'store'])
                        ->name('rules.store');

                    Route::get('/{id}/edit', [RuleController::class, 'edit'])
                        ->name('rules.edit');

                    Route::put('/{id}/update', [RuleController::class, 'update'])
                        ->name('rules.update');

                    Route::put('/{id}/taxonomy/update', [RuleTaxonomyController::class, 'update'])
                        ->name('rules.taxonomy.update');
                });
            });

        Route::name('taxonomies.')
            ->prefix('/taxonomies')
            ->group(function () {
                Route::post('/', [TaxonomyController::class, 'store'])
                    ->name('store');

                Route::put('/{id}', [TaxonomyController::class, 'update'])
                    ->name('update');

                Route::delete('/{id}', [TaxonomyController::class, 'destroy'])
                    ->name('destroy');
            });

        Route::name('terms.')
            ->prefix('/terms')
            ->group(function () {
                Route::post('/', [TermController::class, 'store'])
                    ->name('store');

                Route::put('/{id}', [TermController::class, 'update'])
                    ->name('update');

                Route::delete('/{id}', [TermController::class, 'destroy'])
                    ->name('destroy');
            });
    });


Route::group([
    'middleware' => [
        'auth:sanctum',
        'verified',
        'cache.headers:public;max_age=3600;etag',
    ],
    //'prefix' => 'op/'
],
    function () {
        Route::match(['get', 'post'],'/{jobNumber?}', [\App\Http\Controllers\OPs\JobController::class, 'index'])
            ->name('home')
            ->where('jobNumber', '[0-9]+');
    });
