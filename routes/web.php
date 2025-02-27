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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/get-student', [ScheduleController::class, 'getStudent']);
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
    Route::get('assesment-aspect/edit/{id}', [AssesmentAspectController::class, 'asessmentedit'])->name('assesmentaspect.edit');
    Route::put('assesment-aspect/update/{id}', [AssesmentAspectController::class, 'asessmentupdate'])->name('assesmentaspect.update');
    Route::resource('assesment-category', AssesmentCategoryController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile/{id}', [ProfileController::class, 'index'])->name('profile');
    Route::get('profile-edit/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile-update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');


});


