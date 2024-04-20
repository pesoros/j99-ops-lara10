<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Letter\app\Http\Controllers\LetterApiController;
use App\Http\Controllers\ToolsApiController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('spareparts', [LetterApiController::class, 'spareParts']);
Route::get('trasbus', [LetterApiController::class, 'trasBus']);
Route::get('invoice', [LetterApiController::class, 'invoice']);
Route::get('invoice/{id}', [LetterApiController::class, 'invoiceDetail']);
Route::get('fcm-test', [ToolsApiController::class, 'fcmTest']);
Route::get('busstatus', [ApiController::class, 'busStatus']);
Route::get('employeeready', [ApiController::class, 'employeeReady']);
