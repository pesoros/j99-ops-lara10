<?php

use Illuminate\Support\Facades\Route;
use Modules\Letter\app\Http\Controllers\LetterComplaintController;
use Modules\Letter\app\Http\Controllers\LetterWorkorderController;
use Modules\Letter\app\Http\Controllers\LetterRoadWarrantController;
use Modules\Letter\app\Http\Controllers\LetterPurchaseRequestController;
use Modules\Letter\app\Http\Controllers\LetterGoodsController;
use Modules\Letter\app\Http\Controllers\LetterGoodsReleaseController;

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
        Route::post('complaint/add/createworkorder/{uuid}', [LetterComplaintController::class, 'createWorkorder']);

        Route::get('workorder', [LetterWorkorderController::class, 'listWorkorder']);
        Route::get('workorder/show/detail/{uuid}', [LetterWorkorderController::class, 'detailWorkorder']);
        Route::get('workorder/update/progress/{uuid}', [LetterWorkorderController::class, 'progressWorkorder']);
        Route::get('workorder/update/close/{uuid}', [LetterWorkorderController::class, 'closeWorkorder']);
        Route::post('workorder/update/damagesaction/{uuid}', [LetterWorkorderController::class, 'updateAction']);

        Route::get('roadwarrant', [LetterRoadWarrantController::class, 'listRoadWarrant']);
        Route::get('roadwarrant/add/{book_uuid}', [LetterRoadWarrantController::class, 'addRoadWarrant']);
        Route::post('roadwarrant/add/{book_uuid}', [LetterRoadWarrantController::class, 'addRoadWarrantStore']);
        Route::get('roadwarrant/add', [LetterRoadWarrantController::class, 'addRoadWarrantAkap']);
        Route::post('roadwarrant/add', [LetterRoadWarrantController::class, 'addRoadWarrantAkapStore']);
        Route::get('roadwarrant/edit/{category}/{uuid}', [LetterRoadWarrantController::class, 'editRoadWarrant']);
        Route::post('roadwarrant/edit/{category}/{uuid}', [LetterRoadWarrantController::class, 'editRoadWarrantStore']);
        Route::get('roadwarrant/show/detail/{category}/{uuid}', [LetterRoadWarrantController::class, 'detailRoadWarrant']);
        Route::get('roadwarrant/expense/statusupdate/{category}/{uuid}/{expense_uuid}/{status_id}', [LetterRoadWarrantController::class, 'expenseStatusUpdate']);
        Route::get('roadwarrant/expense/edit/{uuid}', [LetterRoadWarrantController::class, 'editRoadWarrantExpense']);
        Route::post('roadwarrant/expense/edit/{uuid}', [LetterRoadWarrantController::class, 'editRoadWarrantExpenseStore']);

        Route::get('goodsrequest', [LetterGoodsController::class, 'listGoodsRequest']);
        Route::get('goodsrequest/add', [LetterGoodsController::class, 'addGoodsRequest']);
        Route::post('goodsrequest/add', [LetterGoodsController::class, 'addGoodsRequestStore']);
        Route::get('goodsrequest/show/detail/{uuid}', [LetterGoodsController::class, 'detailGoodsRequest']);
        Route::get('goodsrequest/update/progress/{uuid}', [LetterGoodsController::class, 'progressGoodsRequest']);
        Route::get('goodsrequest/update/ready/{uuid}', [LetterGoodsController::class, 'readyGoodsRequest']);
        Route::get('goodsrequest/update/close/{uuid}', [LetterGoodsController::class, 'closeGoodsRequest']);
        Route::post('goodsrequest/update/partsaction/{uuid}', [LetterGoodsController::class, 'updateAction']);

        Route::get('purchaserequest', [LetterPurchaseRequestController::class, 'listPurchaseRequest']);
        Route::get('purchaserequest/add', [LetterPurchaseRequestController::class, 'addPurchaseRequest']);
        Route::post('purchaserequest/add', [LetterPurchaseRequestController::class, 'addPurchaseRequestStore']);
        Route::get('purchaserequest/show/detail/{uuid}', [LetterPurchaseRequestController::class, 'detailPurchaseRequest']);
        Route::post('purchaserequest/update/approval/{uuid}', [LetterPurchaseRequestController::class, 'purchaseRequestApproval']);

        Route::get('goodsrelease', [LetterGoodsReleaseController::class, 'listGoodsRelease']);
        Route::get('goodsrelease/add', [LetterGoodsReleaseController::class, 'addGoodsRelease']);
        Route::post('goodsrelease/add', [LetterGoodsReleaseController::class, 'addGoodsReleaseStore']);
        Route::get('goodsrelease/show/detail/{uuid}', [LetterGoodsReleaseController::class, 'detailGoodsRelease']);
        Route::get('goodsrelease/update/progress/{uuid}', [LetterGoodsReleaseController::class, 'progressGoodsRelease']);
        Route::get('goodsrelease/update/close/{uuid}', [LetterGoodsReleaseController::class, 'closeGoodsRelease']);
        Route::post('goodsrelease/update/partsaction/{uuid}', [LetterGoodsReleaseController::class, 'updateAction']);
    });
});
