<?php

use Illuminate\Support\Facades\Route;
use Modules\Accurate\app\Http\Controllers\AccurateSalesController;
use Modules\Accurate\app\Http\Controllers\AccurateManifestController;

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
    Route::prefix('accurate')->group(function () {
        Route::get('sales', [AccurateSalesController::class, 'index']);
        Route::get('sales/syncbulk', [AccurateSalesController::class, 'syncBulk']);
        Route::get('sales/sync/{bookingcode}', [AccurateSalesController::class, 'sync']);

        Route::get('manifest', [AccurateManifestController::class, 'index']);
        Route::get('manifest/syncbulk', [AccurateManifestController::class, 'syncBulk']);
        Route::get('manifest/sync/{bookingcode}', [AccurateManifestController::class, 'sync']);
    });
});
