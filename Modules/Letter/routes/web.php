<?php

use Illuminate\Support\Facades\Route;
use Modules\Letter\app\Http\Controllers\LetterComplaintController;
use Modules\Letter\app\Http\Controllers\LetterWorkorderController;

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
    Route::prefix('letter')->group(function () {
        Route::get('complaint', [LetterComplaintController::class, 'listComplaint']);
        Route::get('complaint/add', [LetterComplaintController::class, 'addComplaint']);
        Route::post('complaint/add', [LetterComplaintController::class, 'addComplaintStore']);
        Route::get('complaint/show/detail/{uuid}', [LetterComplaintController::class, 'detailComplaint']);
        Route::get('complaint/add/createworkorder/{uuid}', [LetterComplaintController::class, 'createWorkorder']);

        Route::get('workorder', [LetterWorkorderController::class, 'listWorkorder']);
        Route::get('workorder/show/detail/{uuid}', [LetterWorkorderController::class, 'detailWorkorder']);
        Route::get('workorder/update/progress/{uuid}', [LetterWorkorderController::class, 'progressWorkorder']);
        Route::get('workorder/update/close/{uuid}', [LetterWorkorderController::class, 'closeWorkorder']);
        Route::post('workorder/update/damagesaction/{uuid}', [LetterWorkorderController::class, 'updateAction']);
    });
});
