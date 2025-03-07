<?php

use App\Http\Controllers\API\AbsensiController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\InventoryManagementController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('absensi-coach', [AbsensiController::class, 'coachAbsent']);
    Route::post('absensi-student', [AbsensiController::class, 'studentAbsent']);

    Route::get('schedule', [HomeController::class, 'getSchedule']);
    Route::get('schedule/{id}', [HomeController::class, 'getDetailSchedule']);
    Route::post('reschedule', [HomeController::class, 'requestReschedule']);

    Route::get('daftarinventory', [InventoryManagementController::class, 'index']);
    Route::post('borrow-inventory', [InventoryController::class, 'borrowInventory']);

});
