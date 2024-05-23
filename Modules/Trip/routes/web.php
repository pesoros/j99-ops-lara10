<?php

use Illuminate\Support\Facades\Route;
use Modules\Trip\app\Http\Controllers\TripManifestController;
use Modules\Trip\app\Http\Controllers\TripBusStatusController;

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
    Route::prefix('trip')->group(function () {
        Route::get('manifest', [TripManifestController::class, 'listManifest']);
        Route::get('manifest/detail/{id}', [TripManifestController::class, 'detailManifest']);
        Route::get('manifest/open/{id}', [TripManifestController::class, 'openManifest']);
        Route::get('manifest/close/{id}', [TripManifestController::class, 'closeManifest']);
        Route::get('manifest/expenses/{id}', [TripManifestController::class, 'expensesReport']);
        Route::get('manifest/expenses/accept/{id}', [TripManifestController::class, 'expenseAccept']);
        Route::get('manifest/expenses/reject/{id}', [TripManifestController::class, 'expenseReject']);
        Route::get('manifest/expense/edit/{id}/{expenseid}', [TripManifestController::class, 'expenseEdit']);
        Route::post('manifest/expense/edit/{id}/{expenseid}', [TripManifestController::class, 'expenseUpdate']);
        Route::get('manifest/broadcast/{id}', [TripManifestController::class, 'sendWaToPassengers']);
        Route::get('busstatus', [TripBusStatusController::class, 'busStatuskanban']);
    });
});