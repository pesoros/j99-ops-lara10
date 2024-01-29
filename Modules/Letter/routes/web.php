<?php

use Illuminate\Support\Facades\Route;
use Modules\Letter\app\Http\Controllers\LetterComplaintController;
use Modules\Letter\app\Http\Controllers\LetterWorkorderController;
use Modules\Letter\app\Http\Controllers\LetterRoadWarrantController;
use Modules\Letter\app\Http\Controllers\LetterGoodsController;

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
    Route::prefix('letter')->group(function () {
        Route::get('complaint', [LetterComplaintController::class, 'listComplaint']);
        Route::post('complaint/add', [LetterComplaintController::class, 'addComplaintStore']);
        Route::get('complaint/show/detail/{uuid}', [LetterComplaintController::class, 'detailComplaint']);
        Route::get('complaint/add/createworkorder/{uuid}', [LetterComplaintController::class, 'createWorkorder']);

        Route::get('workorder', [LetterWorkorderController::class, 'listWorkorder']);
        Route::get('workorder/show/detail/{uuid}', [LetterWorkorderController::class, 'detailWorkorder']);
        Route::get('workorder/update/progress/{uuid}', [LetterWorkorderController::class, 'progressWorkorder']);
        Route::get('workorder/update/close/{uuid}', [LetterWorkorderController::class, 'closeWorkorder']);
        Route::post('workorder/update/damagesaction/{uuid}', [LetterWorkorderController::class, 'updateAction']);

        Route::get('roadwarrant', [LetterRoadWarrantController::class, 'listRoadWarrant']);
        Route::get('roadwarrant/add/{book_uuid}', [LetterRoadWarrantController::class, 'addRoadWarrant']);
        Route::post('roadwarrant/add/{book_uuid}', [LetterRoadWarrantController::class, 'addRoadWarrantStore']);
        Route::get('roadwarrant/show/detail/{uuid}', [LetterRoadWarrantController::class, 'detailRoadWarrant']);

        Route::get('goodsrequest', [LetterGoodsController::class, 'listGoodsRequest']);
        Route::get('goodsrequest/add', [LetterGoodsController::class, 'addGoodsRequest']);
        Route::post('goodsrequest/add', [LetterGoodsController::class, 'addGoodsRequestStore']);
        Route::get('goodsrequest/show/detail/{uuid}', [LetterGoodsController::class, 'detailGoodsRequest']);
        Route::get('goodsrequest/update/progress/{uuid}', [LetterGoodsController::class, 'progressGoodsRequest']);
        Route::get('goodsrequest/update/close/{uuid}', [LetterGoodsController::class, 'closeGoodsRequest']);
        Route::post('goodsrequest/update/partsaction/{uuid}', [LetterGoodsController::class, 'updateAction']);
    });
});
