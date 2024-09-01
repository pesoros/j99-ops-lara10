<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\app\Http\Controllers\AccurateApiController;
use Modules\Api\app\Http\Controllers\RndApiController;

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
            Route::get('/newtoken', [AccurateApiController::class, 'newtoken']);
            Route::get('/refreshtoken', [AccurateApiController::class, 'refreshtoken']);
            Route::get('/receivetoken/newtoken', [AccurateApiController::class, 'newtokenreceive']);
            Route::get('/receivetoken/refreshtoken', [AccurateApiController::class, 'refreshtokenreceive']);
            Route::get('/dbsession', [AccurateApiController::class, 'dbsession']);
            Route::post('/syncdata', [AccurateApiController::class, 'syncDataCsv']);
        });
        Route::prefix('rnd')->group(function () {
            Route::get('csv/export', [RndApiController::class, 'exportCsv']);
            Route::post('csv/import', [RndApiController::class, 'importCsv']);
            Route::get('xlsx/export', [RndApiController::class, 'exportXlsx']);
            Route::post('xlsx/import', [RndApiController::class, 'importXlsx']);
        });
    });
});