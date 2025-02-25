<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssesmentAspectController;
use App\Http\Controllers\AssesmentCategoryController;
use App\Http\Controllers\AssesmentReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MasterCoachController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;


Route::get('/login-user', [AuthController::class, 'login'])->name('login');
Route::post('/login-check', [AuthController::class, 'loginCheck'])->name('login.loginCheck');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('location', LocationController::class);
    Route::resource('schedule', ScheduleController::class);
    Route::resource('coach', CoachController::class);
    Route::resource('mastercoach', MasterCoachController::class);
    Route::resource('student', StudentController::class);
    Route::resource('assesment-report', AssesmentReportController::class);
    Route::resource('inventory', InventoryController::class);
    Route::get('/inventory-edit/{id}', [InventoryController::class, 'inventedit'])->name('inventory.detailedit');
    Route::put('/inventory-update/{id}', [InventoryController::class, 'inventUpdate'])->name('inventory.detailupdate');
    Route::delete('/inventory-delete/{id}', [InventoryController::class, 'inventDestroy'])->name('inventory.detaildelete');
    Route::resource('admin', AdminController::class);
    Route::resource('assesment-aspect', AssesmentAspectController::class);
    route::get('assesment-aspect/edit/{id}', [AssesmentAspectController::class, 'asessmentedit'])->name('assesmentaspect.edit');
    route::put('assesment-aspect/update/{id}', [AssesmentAspectController::class, 'asessmentupdate'])->name('assesmentaspect.update');
    Route::resource('assesment-category', AssesmentCategoryController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


});


