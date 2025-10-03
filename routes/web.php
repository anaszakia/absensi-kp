<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WorkingHourController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\AttendanceReportController;


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
        // Tambahkan resource lain untuk admin jika diperlukan
            // CRUD Jam Kerja
            Route::resource('working-hours', WorkingHourController::class);
            // Manajemen User untuk Jam Kerja
            Route::get('working-hours/{workingHour}/users', [WorkingHourController::class, 'users'])->name('working-hours.users');
            Route::post('working-hours/{workingHour}/assign-users', [WorkingHourController::class, 'assignUsers'])->name('working-hours.assign-users');
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
    });

Route::redirect('/', '/login');
