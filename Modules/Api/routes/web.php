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
        Route::get('/accurate/newtoken', [AccurateApiController::class, 'newtoken']);
        Route::get('/accurate/refreshtoken', [AccurateApiController::class, 'refreshtoken']);
        Route::get('/accurate/receivetoken/newtoken', [AccurateApiController::class, 'newtokenreceive']);
        Route::get('/accurate/receivetoken/refreshtoken', [AccurateApiController::class, 'refreshtokenreceive']);
        Route::get('/accurate/dbsession', [AccurateApiController::class, 'dbsession']);
    });
});