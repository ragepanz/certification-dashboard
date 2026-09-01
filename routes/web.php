<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Temporary one-click database seeder route for Vercel
Route::get('/seed-data', function () {
    try {
        ini_set('max_execution_time', 300);
        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();
        return response()->json([
            'status' => 'success',
            'message' => 'Semua data pegawai, sertifikasi, dan matriks berhasil di-import ke cloud!',
            'users_count' => \App\Models\User::count(),
            'certifications_count' => \App\Models\Certification::count(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// Redirect root to login / dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Superadmin (LCU) Routes
    Route::middleware('role:superadmin')->group(function () {
        // Certifications Management & Audit
        Route::get('/certifications/template', [CertificationController::class, 'downloadTemplate'])->name('certifications.template');
        Route::get('/certifications/export', [CertificationController::class, 'export'])->name('certifications.export');
        Route::get('/certifications/export-matrix', [CertificationController::class, 'exportMatrix'])->name('certifications.export-matrix');
        Route::post('/certifications/import', [CertificationController::class, 'import'])->name('certifications.import');
        Route::post('/certifications/{certification}/send-reminder', [CertificationController::class, 'sendReminder'])->name('certifications.send-reminder');
        Route::resource('certifications', CertificationController::class);


        // Employee Management
        Route::resource('employees', EmployeeController::class);

        // User / Akun Management
        Route::resource('users', UserController::class);

        // Reminder Settings
        Route::get('/settings/reminder', [SettingController::class, 'reminder'])->name('settings.reminder');
        Route::put('/settings/reminder', [SettingController::class, 'updateReminder'])->name('settings.reminder.update');
        Route::post('/settings/reminder/trigger', [SettingController::class, 'triggerManualReminders'])->name('settings.reminder.trigger');

        // Job Training Matrix (TrainingMan)
        Route::get('/settings/matrix', [\App\Http\Controllers\JobTrainingMatrixController::class, 'index'])->name('matrix.index');
        Route::post('/settings/matrix', [\App\Http\Controllers\JobTrainingMatrixController::class, 'store'])->name('matrix.store');
        Route::put('/settings/matrix/{matrix}', [\App\Http\Controllers\JobTrainingMatrixController::class, 'update'])->name('matrix.update');
        Route::delete('/settings/matrix/{matrix}', [\App\Http\Controllers\JobTrainingMatrixController::class, 'destroy'])->name('matrix.destroy');

        // Certificate Types Directory & Analytics
        Route::get('/certificate-types', [\App\Http\Controllers\CertificateTypeController::class, 'index'])->name('certificate-types.index');
        Route::get('/certificate-types/{name}', [\App\Http\Controllers\CertificateTypeController::class, 'show'])->name('certificate-types.show');

        // Reports & Exports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('/reports/print', [ReportController::class, 'printView'])->name('reports.print');
    });
});

