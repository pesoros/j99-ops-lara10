<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\app\Http\Controllers\SalesAkapController;

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
    Route::prefix('sales')->group(function () {
        Route::get('akap', [SalesAkapController::class, 'listAkap']);
        Route::get('pariwisata', [SalesAkapController::class, 'listPariwisata']);
    });
});
