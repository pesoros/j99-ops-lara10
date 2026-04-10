<?php

use Illuminate\Support\Facades\Route;
use Modules\Inspection\app\Http\Controllers\FitCheckController;

Route::middleware(['auth', 'has-permission'])->group(function () {
    Route::prefix('inspection')->group(function () {
        Route::get('fit-check', [FitCheckController::class, 'index']);
        Route::get('fit-check/add', [FitCheckController::class, 'add']);
        Route::post('fit-check/add', [FitCheckController::class, 'store']);
        Route::get('fit-check/edit/{id}', [FitCheckController::class, 'edit']);
        Route::post('fit-check/edit/{id}', [FitCheckController::class, 'update']);
        Route::get('fit-check/delete/{id}', [FitCheckController::class, 'delete']);
        Route::get('fit-check/print/{id}', [FitCheckController::class, 'print']);
    });
});