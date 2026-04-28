<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

// ==================== LOGIN ====================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ROUTE EMPLOYEE (Karyawan) ====================
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::get('/absensi', [EmployeeController::class, 'absensi'])->name('absensi');
    Route::get('/task', [EmployeeController::class, 'task'])->name('task');
    Route::get('/poin', [EmployeeController::class, 'poin'])->name('poin');
    Route::get('/gaji', [EmployeeController::class, 'gaji'])->name('gaji');
});

// ==================== ROUTE HRD ====================
Route::get('/', fn() => redirect()->route('dashboard'));
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
Route::get('/karyawan', [PageController::class, 'karyawan'])->name('karyawan');
Route::get('/project', [PageController::class, 'project'])->name('project.index');
Route::get('/task-hrd', [PageController::class, 'task'])->name('task.index'); // ubah nama biar beda dengan employee task
Route::get('/kelola-poin', [PageController::class, 'kelolaPoin'])->name('kelola-poin');
Route::get('/kelola-gaji', [PageController::class, 'kelolaGaji'])->name('kelola-gaji');