<?php

use App\Http\Controllers\CoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MasterCoachController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::resource('location', LocationController::class);
Route::resource('schedule', ScheduleController::class);
Route::resource('coach', CoachController::class);
Route::resource('mastercoach', MasterCoachController::class);



