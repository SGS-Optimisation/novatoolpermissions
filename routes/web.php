<?php

use App\Http\Controllers\PMs\AuditActivityController;
use App\Http\Controllers\CurrentTeamController;
use App\Http\Controllers\PMs\ClientAccountController;
use App\Http\Controllers\PMs\ClientAccountTaxonomyController;
use App\Http\Controllers\PMs\PmHomeController;
use App\Http\Controllers\PMs\Rules\RuleAttachmentController;
use App\Http\Controllers\PMs\Rules\RuleController;
use App\Http\Controllers\PMs\Rules\RuleTaxonomyController;
use App\Http\Controllers\PMs\TaxonomyController;
use App\Http\Controllers\PMs\TeamController;
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

Route::get('/login/azure/callback', [\App\Http\Controllers\Auth\AzureAuthController::class, 'handleOauthResponse'])
    ->middleware(['web']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', function () {
        return redirect(route('home'));
    })->name('dashboard');

    Route::put('/current-team', [CurrentTeamController::class, 'update'])
        ->name('current-team.update');

    Route::get('/{jobNumber?}', [\App\Http\Controllers\OPs\JobController::class, 'show'])
        ->name('job.rules')
        ->where('jobNumber', '[0-9\-]+');

    Route::get('/{jobNumber}/status', [\App\Http\Controllers\OPs\JobController::class, 'status'])
        ->name('job.rules.status')
        ->where('jobNumber', '[0-9\-]+');

    Route::post('/{jobNumber?}', [\App\Http\Controllers\OPs\JobController::class, 'search'])
        ->name('job.search')
        ->where('jobNumber', '[0-9\-]+');


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
            ->group(function () {

                Route::get('/client-account/create', [ClientAccountController::class, 'create'])
                    ->name('create');

                Route::post('/client-account', [ClientAccountController::class, 'store'])
                    ->name('store');

                Route::get('/{id}', [ClientAccountController::class, 'getById'])
                    ->where('id', '[0-9]+')
                    ->name('getById');

                Route::prefix('/{clientAccount:slug}')
                    ->middleware(['team.autoswitch'])
                    ->group(function () {
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
                        Route::name('rules.')->prefix('/rules')
                            ->group(function () {

                                Route::get('/', [RuleController::class, 'index'])
                                    //->middleware('cache.headers:public;max_age=300;etag')
                                    ->name('index');

                                Route::get('/create', [RuleController::class, 'create'])
                                    ->name('create');

                                Route::post('/store', [RuleController::class, 'store'])
                                    ->name('store');

                                Route::post('/publish', [RuleController::class, 'massPublish'])
                                    ->name('publish');

                                Route::post('/unpublish', [RuleController::class, 'massUnpublish'])
                                    ->name('unpublish');

                                Route::get('/{id}/edit', [RuleController::class, 'edit'])
                                    ->name('edit');

                                Route::delete('/{id}/delete', [RuleController::class, 'destroy'])
                                    ->name('delete');

                                Route::put('/{id}/restore', [RuleController::class, 'restore'])
                                    ->name('restore');

                                Route::get('/{id}/history', [AuditActivityController::class, 'ruleHistory'])
                                    ->name('history');

                                Route::put('/{id}/update', [RuleController::class, 'update'])
                                    ->name('update');

                                Route::put('/{id}/taxonomy/update', [RuleTaxonomyController::class, 'update'])
                                    ->name('taxonomy.update');


                                /*
                                 * Rule Attachments section
                                 */
                                Route::name('attachments.')->prefix('/{id}/attachments')
                                    ->group(function () {

                                        Route::post('/', [RuleAttachmentController::class, 'attach'])
                                            ->name('store');

                                        Route::delete('/{attachment}', [RuleAttachmentController::class, 'delete'])
                                            ->name('delete');
                                    });


                            });

                        /*
                         * Teams section
                         */
                        Route::name('teams.')->prefix('/teams')
                            ->group(function () {
                                Route::get('/show/{teamId}', [TeamController::class, 'show'])
                                    ->name('show');

                                Route::get('/create', [TeamController::class, 'create'])
                                    ->name('create');

                                Route::post('/store', [TeamController::class, 'store'])
                                    ->name('store');
                            });

                        /*
                         * Stats section
                         */
                        Route::name('stats.')->prefix('/stats')
                            ->group(function () {
                                Route::get('/jobs', [\App\Http\Controllers\Stats\StatsController::class, 'jobs'])
                                    ->name('jobs');

                                Route::get('/rules', [\App\Http\Controllers\Stats\StatsController::class, 'rules'])
                                    ->name('rules');
                            });
                    });


            });

        Route::name('taxonomies.')
            ->prefix('/taxonomies')
            ->group(function () {
                Route::post('/', [TaxonomyController::class, 'store'])
                    ->name('store');

                Route::put('/{id}/update', [TaxonomyController::class, 'update'])
                    ->name('update');

                Route::put('/{id}/destroy', [TaxonomyController::class, 'destroy'])
                    ->name('destroy');
            });

        Route::name('terms.')
            ->prefix('/terms')
            ->group(function () {
                Route::post('/', [TermController::class, 'store'])
                    ->name('store');

                Route::put('/{id}/update', [TermController::class, 'update'])
                    ->name('update');

                Route::put('/{id}/delete', [TermController::class, 'destroy'])
                    ->name('destroy');
            });
    });

Route::name('stats.')
    ->prefix('stats/')
    ->middleware([
        'auth:sanctum',
        'verified',
        //'cache.headers:public;max_age=3600;etag',
    ])->group(function () {

        Route::get('/rules', [\App\Http\Controllers\Stats\StatsController::class, 'rules'])
            ->name('rules');

        Route::get('/jobs', [\App\Http\Controllers\Stats\StatsController::class, 'jobs'])
            ->name('jobs');
    });

Route::get('nova/login', function () {
    return redirect('/login/microsoft');
})->name('nova.login');

Route::match(['get', 'post'], 'nova/logout', function () {
    return redirect('logout');
})->name('nova.logout');





