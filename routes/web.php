<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\UserScheduleController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\WorkingHourController;


// hanya bisa diakses tamu (belum login)
Route::middleware('guest')->group(function () {
    // Form login
    Route::get('/login', [LoginController::class, 'showLoginForm'])
         ->name('login');

    // Proses login
    Route::post('/login', [LoginController::class, 'login'])
         ->middleware('log.sensitive')
         ->name('login.submit');

    // Form register
    Route::get('/register', [LoginController::class, 'showRegisterForm'])
         ->name('register');

    // Proses register
    Route::post('/register', [LoginController::class, 'register'])
         ->middleware('log.sensitive')
         ->name('register.submit');
});

// Logout (method POST demi keamanan; pakai @csrf di form logout)
Route::post('/logout', [LoginController::class, 'logout'])
     ->middleware(['auth', 'log.sensitive'])
     ->name('logout');



// Profile routes untuk admin & user, tetap pakai log.sensitive
Route::middleware(['auth', 'log.sensitive'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// auth admin
Route::middleware(['auth', 'role:admin', 'log.sensitive'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::resource('users', UserController::class);
        // Audit Log routes
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/audit/{auditLog}', [AuditLogController::class, 'show'])->name('audit.show');
        Route::post('/audit/export', [AuditLogController::class, 'export'])->name('audit.export');
        // Rekap Absensi
        Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/export', [AttendanceReportController::class, 'export'])->name('attendance.export');
        // Leave Request Management
        Route::get('/leave-requests', [LeaveRequestController::class, 'adminIndex'])->name('leave-requests.index');
        Route::post('/leave-requests/{leaveRequest}/action', [LeaveRequestController::class, 'adminAction'])->name('leave-requests.action');
        
        // Working Hours Management
        Route::resource('working-hours', WorkingHourController::class);
        
        // Subject Management
        Route::resource('subjects', SubjectController::class);
        
        // User Schedule Management
        Route::get('schedules', [AdminScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/create', [AdminScheduleController::class, 'create'])->name('schedules.create');
        Route::get('schedules/check-conflicts', [AdminScheduleController::class, 'checkConflicts'])->name('schedules.check-conflicts');
        Route::post('schedules', [AdminScheduleController::class, 'store'])->name('schedules.store');
        Route::get('schedules/{schedule}', [AdminScheduleController::class, 'show'])->name('schedules.show');
        Route::get('schedules/{schedule}/edit', [AdminScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('schedules/{schedule}', [AdminScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('schedules/{schedule}', [AdminScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

// auth user
Route::middleware(['auth', 'role:user', 'log.sensitive'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        // Ganti dashboard ke AttendanceController
        Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        
        // Attendance Routes
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
        Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
        
        // Leave Request Routes
        Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
        Route::get('/leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
        Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
        
        // User Schedule Routes
        Route::get('/schedules', [UserScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/calendar', [UserScheduleController::class, 'calendar'])->name('schedules.calendar');
    });

Route::redirect('/', '/login');
