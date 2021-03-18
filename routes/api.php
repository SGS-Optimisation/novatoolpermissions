<?php

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


Route::group([
    'auth',
    'prefix' => 'rule/'
], function () {
    Route::get('search/{jobNumber}', [\App\Http\Controllers\OPs\JobController::class, 'search'])
        ->name('rule_search');
});

Route::get('audit', [\App\Http\Controllers\Api\AuditActivityController::class, 'show'])->name('audit');
