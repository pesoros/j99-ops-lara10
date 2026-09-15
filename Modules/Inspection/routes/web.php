<?php

use Illuminate\Support\Facades\Route;
use Modules\Inspection\app\Http\Controllers\FitCheckController;

Route::middleware(['auth', 'has-permission'])->group(function () {
    Route::prefix('inspection')->group(function () {
        Route::get('fit-check', [FitCheckController::class, 'index']);

        // Langkah 1 - dokter mengisi pemeriksaan
        Route::get('fit-check/add', [FitCheckController::class, 'add']);
        Route::post('fit-check/add', [FitCheckController::class, 'store']);

        // Langkah 2 - pengemudi membaca hasil lalu menandatangani.
        // Kata "add"/"edit" sengaja tetap berada di segmen ke-3: middleware
        // hasPermission mengambil hak akses dari request()->segment(3), jadi
        // "fit-check/confirm" akan jatuh ke hak 'edit' dan memblokir petugas
        // yang hanya punya hak 'add' di tengah alur.
        Route::get('fit-check/add/confirm', [FitCheckController::class, 'addConfirm']);
        Route::post('fit-check/add/confirm', [FitCheckController::class, 'addConfirmStore']);

        Route::get('fit-check/edit/{id}', [FitCheckController::class, 'edit']);
        Route::post('fit-check/edit/{id}', [FitCheckController::class, 'update']);
        Route::get('fit-check/edit/{id}/confirm', [FitCheckController::class, 'editConfirm']);
        Route::post('fit-check/edit/{id}/confirm', [FitCheckController::class, 'editConfirmStore']);

        Route::get('fit-check/delete/{id}', [FitCheckController::class, 'delete']);
        Route::get('fit-check/print/{id}', [FitCheckController::class, 'print']);
    });
});
