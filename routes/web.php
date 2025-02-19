<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssesmentAspectController;
use App\Http\Controllers\AssesmentCategoryController;
use App\Http\Controllers\AssesmentReportController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MasterCoachController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::resource('location', LocationController::class);
Route::resource('schedule', ScheduleController::class);
Route::resource('coach', CoachController::class);
Route::resource('mastercoach', MasterCoachController::class);
Route::resource('student', StudentController::class);
Route::resource('assesment-report', AssesmentReportController::class);
Route::resource('inventory', InventoryController::class);
Route::resource('admin', AdminController::class);
Route::resource('assesment-aspect', AssesmentAspectController::class);
Route::resource('assesment-category', AssesmentCategoryController::class);



