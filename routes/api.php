<?php

use App\Http\Controllers\API\AbsensiController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\InventarisStokController;
use App\Http\Controllers\API\InventoryManagementController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('daftarinventory', [InventoryManagementController::class, 'index']);
    Route::post('absensi', [AbsensiController::class, 'store']);
});
