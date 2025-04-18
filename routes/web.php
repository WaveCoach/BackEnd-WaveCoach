<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssesmentAspectController;
use App\Http\Controllers\AssesmentCategoryController;
use App\Http\Controllers\AssesmentReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeletedInventoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventorylandingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MasterCoachController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RescheduleRequestController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScheduleImportController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Announcement;
use App\Models\RescheduleRequest;
use Illuminate\Support\Facades\Route;


Route::get('/login-user', [AuthController::class, 'login'])->name('login');
Route::post('/login-check', [AuthController::class, 'loginCheck'])->name('login.loginCheck');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/student-attendance', [AttendanceController::class, 'studentAttendance'])->name('attendance.student');
    Route::get('/student-attendance/{id}', [AttendanceController::class, 'studentAttendanceShow'])->name('attendance.student.show');
    Route::get('/coach-attendance', [AttendanceController::class, 'coachAttendance'])->name('attendance.coach');
    Route::get('/coach-attendance/{id}', [AttendanceController::class, 'coachAttendanceShow'])->name('attendance.coach.show');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('location', LocationController::class);
    Route::post('/import-location', [LocationController::class, 'import'])->name('import.location');
    Route::get('/import-location', [LocationController::class, 'importCreate'])->name('import.location.create');
    Route::get('/export-location', [LocationController::class, 'exportLocations'])->name('export.location');
    Route::resource('schedule', ScheduleController::class);
    Route::resource('reschedule', RescheduleRequestController::class);
    Route::get('/get-student', [ScheduleController::class, 'getStudent']);
    Route::resource('coach', CoachController::class);
    Route::get('coach-export', [CoachController::class, 'coachExport'])->name('coach.export');
    Route::get('form-coach-import', [CoachController::class, 'formCoachImport'])->name('coach.formimport');
    Route::post('coach-import', [CoachController::class, 'coachImport'])->name('coach.import');
    Route::put('reset-password/{id}', [CoachController::class, 'resetPassword'])->name('coach.updatePassword');
    Route::resource('mastercoach', MasterCoachController::class);
    Route::resource('announcement', AnnouncementController::class);
    Route::resource('student', StudentController::class);
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/students/form-import', [StudentController::class, 'formImport'])->name('students.formimport');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('assesment-report', AssesmentReportController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('inventory-landing-history', InventorylandingController::class);
    Route::resource('deleted-inventory', DeletedInventoryController::class);
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
    Route::post('/import-schedule', [ScheduleImportController::class, 'import'])->name('import.schedule');
    Route::get('/upload-schedule', [ScheduleController::class, 'createExcel'])->name('importSchedule.create');
});


