<?php

use App\Http\Controllers\Api\SimplifiedCustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::name('api.')
    ->middleware([
        'auth:sanctum',
        //'verified',
        //'user.permissions',
        //'cache.headers:public;max_age=3600;etag',
    ])
    ->group(function () {

        Route::name('simplified-customer.')
            ->prefix('/simplified-customer')
            ->group(function () {

                Route::get('/', [SimplifiedCustomerController::class, 'index'])->name('index');
            });

        Route::get('pm/', [\App\Http\Controllers\Api\PmHomeController::class, 'index'])
            ->name('pm.landing');

        Route::get('pm/{clientAccount:slug}/rules', [\App\Http\Controllers\Api\RuleController::class, 'index'])
            ->name('pm.client-account.rules');

        Route::get('pm/{clientAccount:slug}/taxonomy', [\App\Http\Controllers\Api\TaxonomyController::class, 'show'])
            ->name('pm.client-account.taxonomy');
    });
