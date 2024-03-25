<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\app\Http\Controllers\EmployeeCrewController;

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
    Route::prefix('employee')->group(function () {
        Route::get('crew', [EmployeeCrewController::class, 'listCrew']);
        Route::get('crew/add', [EmployeeCrewController::class, 'addCrew']);
        Route::post('crew/add', [EmployeeCrewController::class, 'addCrewStore']);
        Route::get('crew/edit/{uuid}', [EmployeeCrewController::class, 'editCrew']);
        Route::post('crew/edit/{uuid}', [EmployeeCrewController::class, 'editCrewStore']);
        Route::get('crew/detail/{uuid}', [EmployeeCrewController::class, 'detailCrew']);
    });
});