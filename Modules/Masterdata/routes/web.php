<?php

use Illuminate\Support\Facades\Route;
use Modules\Masterdata\app\Http\Controllers\MasterdataPartsAreaController;
use Modules\Masterdata\app\Http\Controllers\MasterdataPartsScopeController;
use Modules\Masterdata\app\Http\Controllers\MasterdataBusController;
use Modules\Masterdata\app\Http\Controllers\MasterdataClassController;
use Modules\Masterdata\app\Http\Controllers\MasterdataSparePartsController;

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
        Route::get('partsarea/edit/{uuid}', [MasterdataPartsAreaController::class, 'editMasterPartsArea']);
        Route::post('partsarea/edit/{uuid}', [MasterdataPartsAreaController::class, 'editMasterPartsAreaUpdate']);
        Route::get('partsarea/delete/{uuid}', [MasterdataPartsAreaController::class, 'deleteMasterPartsArea']);
        Route::get('partsscope', [MasterdataPartsScopeController::class, 'listMasterPartsScope']);
        Route::get('partsscope/add', [MasterdataPartsScopeController::class, 'addMasterPartsScope']);
        Route::post('partsscope/add', [MasterdataPartsScopeController::class, 'addMasterPartsScopeStore']);
        Route::get('bus', [MasterdataBusController::class, 'listMasterBus']);
        Route::get('bus/add', [MasterdataBusController::class, 'addMasterBus']);
        Route::post('bus/add', [MasterdataBusController::class, 'addMasterBusStore']);
        Route::get('bus/edit/{uuid}', [MasterdataBusController::class, 'editMasterBus']);
        Route::post('bus/edit/{uuid}', [MasterdataBusController::class, 'editMasterBusUpdate']);
        Route::get('bus/delete/{uuid}', [MasterdataBusController::class, 'deleteMasterBus']);
        Route::get('class', [MasterdataClassController::class, 'listMasterClass']);
        Route::get('class/add', [MasterdataClassController::class, 'addMasterClass']);
        Route::post('class/add', [MasterdataClassController::class, 'addMasterClassStore']);
        Route::get('class/edit/{uuid}', [MasterdataClassController::class, 'editMasterClass']);
        Route::post('class/edit/{uuid}', [MasterdataClassController::class, 'editMasterClassUpdate']);
        Route::get('class/delete/{uuid}', [MasterdataClassController::class, 'deleteMasterClass']);
        Route::get('spareparts', [MasterdataSparePartsController::class, 'listMasterSpareParts']);
    });
});