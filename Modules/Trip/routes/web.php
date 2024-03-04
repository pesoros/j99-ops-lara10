<?php

use Illuminate\Support\Facades\Route;
use Modules\Trip\app\Http\Controllers\TripManifestController;

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
        Route::get('manifest/detail/{id}', [TripManifestController::class, 'listManifest']);
        Route::get('manifest/expenses/{id}', [TripManifestController::class, 'listManifest']);
        Route::get('manifest/activate/{id}', [TripManifestController::class, 'listManifest']);
        Route::get('manifest/deactivate/{id}', [TripManifestController::class, 'listManifest']);
    });
});