<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\PaymentReceiptController as FinancePaymentReceiptController;
use App\Http\Controllers\Finance\TuitionConfigController;
use App\Http\Controllers\Parent\MedicalJustificationController;
use App\Http\Controllers\Parent\PaymentReceiptController as ParentPaymentReceiptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\GradeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para Padres de Familia
    Route::middleware(['role:parent'])->prefix('parent')->name('parent.')->group(function () {
        Route::get('payment-receipts', [ParentPaymentReceiptController::class, 'index'])->name('payment-receipts.index');
        Route::get('payment-receipts/create', [ParentPaymentReceiptController::class, 'create'])->name('payment-receipts.create');
        Route::post('payment-receipts', [ParentPaymentReceiptController::class, 'store'])->name('payment-receipts.store');
        Route::get('payment-receipts/{paymentReceipt}', [ParentPaymentReceiptController::class, 'show'])->name('payment-receipts.show');

        Route::get('medical-justifications', [MedicalJustificationController::class, 'index'])->name('medical-justifications.index');
        Route::get('medical-justifications/create', [MedicalJustificationController::class, 'create'])->name('medical-justifications.create');
        Route::post('medical-justifications', [MedicalJustificationController::class, 'store'])->name('medical-justifications.store');
    });

    // Rutas para Administrador de Finanzas
    Route::middleware(['role:finance_admin'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('payment-receipts', [FinancePaymentReceiptController::class, 'index'])->name('payment-receipts.index');
        Route::get('payment-receipts/{paymentReceipt}', [FinancePaymentReceiptController::class, 'show'])->name('payment-receipts.show');
        Route::post('payment-receipts/{paymentReceipt}/validate', [FinancePaymentReceiptController::class, 'validate'])->name('payment-receipts.validate');
        Route::post('payment-receipts/{paymentReceipt}/reject', [FinancePaymentReceiptController::class, 'reject'])->name('payment-receipts.reject');

        Route::resource('tuition-configs', TuitionConfigController::class)->except(['show']);
    });

    // Rutas para Maestros
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::resource('grades', GradeController::class);
        Route::resource('assignments', AssignmentController::class);
        Route::resource('announcements', AnnouncementController::class);
    });

    // Rutas para Estudiantes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('schedule', [StudentDashboardController::class, 'schedule'])->name('schedule');
        Route::get('grades', [StudentDashboardController::class, 'grades'])->name('grades');
        Route::get('assignments', [StudentDashboardController::class, 'assignments'])->name('assignments');
    });
});

require __DIR__.'/auth.php';
