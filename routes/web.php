<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller2;

// Docs: Super Admin Controllers
use App\Http\Controllers\SuperAdmin\v1\UserController;
use App\Http\Controllers\SuperAdmin\v1\TeacherController;
use App\Http\Controllers\SuperAdmin\v1\StudentController;

// Docs: Organisation Admin + Supervisor - NOTUN
use App\Http\Controllers\OrganisationAdmin\DashboardController;
use App\Http\Controllers\Supervisor\SupervisorController;

// Docs: Student Self Attendance - NOTUN - Eta age chilo na
use App\Http\Controllers\Student\AttendanceController;

Route::redirect('/', '/login');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Organisation setup (/setup kept as an alias so old links still work)
Route::get('/organisation/setup', [AuthController::class, 'showSetup'])->name('setup');
Route::post('/organisation/setup', [AuthController::class, 'register'])->name('setup.store');
Route::redirect('/setup', '/organisation/setup');

// FIX: /dashboard e gele 404 asbe na, role dekhe thik dashboard e jabe
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('supervisor')) {
        return redirect()->route('supervisor.dashboard');
    }

    if ($user->hasRole('organisation_admin')) {
        return redirect()->route('organisationadmin.dashboard');
    }

    if ($user->hasRole('super_admin')) {
        return redirect()->route('superadmin.users.index');
    }

    if ($user->hasRole('student')) {
        return redirect()->route('student.attendance.create');
    }

    return redirect()->route('login');
})->name('dashboard');

// Super Admin - Docs: Ekta group e sob
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'listUser'])->name('superadmin.users.index');
    Route::get('/admin/users/{uuid}', [UserController::class, 'getUser'])
        ->whereUuid('uuid')->name('superadmin.users.show');

    // Teacher
    Route::get('/admin/teachers', [TeacherController::class, 'index'])->name('superadmin.teachers.index');
    Route::get('/admin/teachers/create', [TeacherController::class, 'create'])->name('superadmin.teachers.create');
    Route::post('/admin/teachers', [TeacherController::class, 'store'])->name('superadmin.teachers.store');
    Route::get('/admin/teachers/{id}', [TeacherController::class, 'show'])->name('superadmin.teachers.show');
    Route::delete('/admin/teachers/{id}', [TeacherController::class, 'destroy'])->name('superadmin.teachers.destroy');

    // Student
    Route::get('/admin/students', [StudentController::class, 'index'])->name('superadmin.students.index');
    Route::get('/admin/students/create', [StudentController::class, 'create'])->name('superadmin.students.create');
    Route::post('/admin/students', [StudentController::class, 'store'])->name('superadmin.students.store');
    Route::get('/admin/students/{id}', [StudentController::class, 'show'])->name('superadmin.students.show');
    Route::delete('/admin/students/{id}', [StudentController::class, 'destroy'])->name('superadmin.students.destroy');
});

// Organisation Admin - Attendance Nebe - ID Set + Face Scan + Box
// FINAL NAM: organisationadmin - sob jaygay eki, kono hyphen nei, kono org. nei
Route::middleware(['auth'])->prefix('organisationadmin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('organisationadmin.dashboard');
    Route::post('/students', [DashboardController::class, 'storeStudent'])->name('organisationadmin.student.store');
    Route::put('/students/{id}', [DashboardController::class, 'updateStudent'])->name('organisationadmin.student.update');
    Route::delete('/students/{id}', [DashboardController::class, 'deleteStudent'])->name('organisationadmin.student.delete');
    // Attendance - Eta na thakle Box asbe na
    Route::post('/attendance', [DashboardController::class, 'markAttendance'])->name('organisationadmin.attendance');
});

// Supervisor - Docs er moto + Window OPEN/CLOSE - NOTUN
Route::middleware(['auth'])->prefix('supervisor')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
    Route::get('/students', [SupervisorController::class, 'studentList'])->name('supervisor.students');
    Route::get('/students/{id}', [SupervisorController::class, 'show'])->name('supervisor.students.show');
    Route::put('/students/{id}', [SupervisorController::class, 'update'])->name('supervisor.students.update');
    Route::delete('/students/{id}', [SupervisorController::class, 'destroy'])->name('supervisor.students.delete');

    // Window - Supervisor khulbe, Student dite parbe
    Route::post('/window/open', [SupervisorController::class, 'openWindow'])->name('supervisor.window.open');
    Route::post('/window/close', [SupervisorController::class, 'closeWindow'])->name('supervisor.window.close');
});

// Student - Self Attendance - NOTUN - Window khullei parbe
Route::middleware(['auth'])->prefix('student')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('student.attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('student.attendance.store');
    Route::get('/history', [AttendanceController::class, 'history'])->name('student.history');
});

Route::get('/test-policy', [Controller2::class, 'index']);