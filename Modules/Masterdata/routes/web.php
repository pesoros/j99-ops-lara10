<?php

use Illuminate\Support\Facades\Route;
use Modules\Masterdata\app\Http\Controllers\MasterdataBusController;
use Modules\Masterdata\app\Http\Controllers\MasterdataClassController;
use Modules\Masterdata\app\Http\Controllers\MasterdataFacilitiesController;

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
        Route::get('complaintscope', [MasterdataBusController::class, 'listMasterComplaintScope']);
        Route::get('complaintscope/add', [MasterdataBusController::class, 'addMasterComplaintScope']);
        Route::post('complaintscope/add', [MasterdataBusController::class, 'addMasterComplaintScopeStore']);
    });
});