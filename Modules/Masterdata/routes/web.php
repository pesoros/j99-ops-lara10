<?php

use Illuminate\Support\Facades\Route;
use Modules\Masterdata\app\Http\Controllers\MasterdataPartsAreaController;
use Modules\Masterdata\app\Http\Controllers\MasterdataPartsScopeController;

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

Route::middleware(['auth','has-permission'])->group(function () {
    Route::prefix('masterdata')->group(function () {
        Route::get('partsarea', [MasterdataPartsAreaController::class, 'listMasterPartsArea']);
        Route::get('partsarea/add', [MasterdataPartsAreaController::class, 'addMasterPartsArea']);
        Route::post('partsarea/add', [MasterdataPartsAreaController::class, 'addMasterPartsAreaStore']);
        Route::get('partsscope', [MasterdataPartsScopeController::class, 'listMasterPartsScope']);
        Route::get('partsscope/add', [MasterdataPartsScopeController::class, 'addMasterPartsScope']);
        Route::post('partsscope/add', [MasterdataPartsScopeController::class, 'addMasterPartsScopeStore']);
    });
});