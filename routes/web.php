<?php

use App\Http\Controllers\PMs\AuditActivityController;
use App\Http\Controllers\CurrentTeamController;
use App\Http\Controllers\PMs\ClientAccountController;
use App\Http\Controllers\PMs\ClientAccountTaxonomyController;
use App\Http\Controllers\PMs\PmHomeController;
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

/**
 * When user is unauthenticated, route '/' is handled by Fortify and leads to /login page
 */

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', function () {
        return redirect(route('home'));
    })->name('dashboard');

    Route::put('/current-team', [CurrentTeamController::class, 'update'])
        ->name('current-team.update');

    Route::get( '/{jobNumber?}', [\App\Http\Controllers\OPs\JobController::class, 'show'])
        ->name('job.rules')
        ->where('jobNumber', '[0-9]+');

    Route::post( '/{jobNumber?}', [\App\Http\Controllers\OPs\JobController::class, 'search'])
        ->name('job.search')
        ->where('jobNumber', '[0-9]+');


    Route::post('/rule/{rule}/flag', [\App\Http\Controllers\RuleFlaggingController::class, 'flag'])
        ->name('rule.flag')
        ->where('rule', '[0-9]+');

    Route::post('/rule/{rule}/unflag', [\App\Http\Controllers\RuleFlaggingController::class, 'unflag'])
        ->name('rule.unflag')
        ->where('rule', '[0-9]+');
});

Route::name('pm.')
    ->prefix('pm/')
    ->middleware([
        'auth:sanctum',
        'verified',
        //'cache.headers:public;max_age=3600;etag',
    ])->group(function () {

        Route::get('/', [PmHomeController::class, 'index'])
            ->name('landing');

        Route::name('client-account.')
            //->prefix('/{clientAccount:slug}')
            ->group(function () {

                Route::get('/client-account/create', [ClientAccountController::class, 'create'])
                    ->name('create');

                Route::post('/client-account', [ClientAccountController::class, 'store'])
                    ->name('store');

                Route::prefix('/{clientAccount:slug}')->group(function () {
                    Route::get('/', [ClientAccountController::class, 'show']);

                    Route::get('/dashboard', [ClientAccountController::class, 'show'])
                        ->name('dashboard');

                    Route::get('/edit', [ClientAccountController::class, 'edit'])
                        ->name('edit');

                    Route::post('/update', [ClientAccountController::class, 'update'])
                        ->name('update');

                    /*
                     * Configuration section
                     */
                    Route::group(['prefix' => '/taxonomy'], function () {
                        Route::get('/', [ClientAccountTaxonomyController::class, 'show'])
                            //->middleware('cache.headers:public;max_age=300;etag')
                            ->name('taxonomy');
                    });

                    /*
                     * Rules section
                     */
                    Route::group(['prefix' => '/rules'], function () {

                        Route::get('/', [RuleController::class, 'index'])
                            //->middleware('cache.headers:public;max_age=300;etag')
                            ->name('rules');

                        Route::get('/create', [RuleController::class, 'create'])
                            ->name('rules.create');

                        Route::post('/store', [RuleController::class, 'store'])
                            ->name('rules.store');

                        Route::get('/{id}/edit', [RuleController::class, 'edit'])
                            ->name('rules.edit');

                        Route::get('/{id}/history', [AuditActivityController::class, 'ruleHistory'])
                            ->name('rules.history');

                        Route::put('/{id}/update', [RuleController::class, 'update'])
                            ->name('rules.update');

                        Route::put('/{id}/taxonomy/update', [RuleTaxonomyController::class, 'update'])
                            ->name('rules.taxonomy.update');
                    });
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


Route::get('nova/login', function () {
    return redirect('/login/microsoft');
})->name('nova.login');

Route::get('nova/logout', function () {
    return redirect('logout');
})->name('nova.logout');





