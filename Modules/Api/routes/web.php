<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\app\Http\Controllers\AccurateApiController;

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

Route::group([], function () {
    Route::prefix('api')->group(function () {
        Route::prefix('accurate')->group(function () {
            // refreshtoken and dbsession stay open: DashboardController calls them
            // server-side with no session. receivetoken/* is Accurate's OAuth redirect.
            Route::get('/newtoken', [AccurateApiController::class, 'newtoken'])->middleware('auth');
            Route::get('/refreshtoken', [AccurateApiController::class, 'refreshtoken']);
            Route::get('/receivetoken/newtoken', [AccurateApiController::class, 'newtokenreceive']);
            Route::get('/receivetoken/refreshtoken', [AccurateApiController::class, 'refreshtokenreceive']);
            Route::get('/dbsession', [AccurateApiController::class, 'dbsession']);
            Route::post('/syncdata', [AccurateApiController::class, 'syncDataCsv'])->middleware('auth');
        });
    });
});