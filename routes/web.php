<?php

use App\Http\Controllers\Admin\GradeOverviewController;
use App\Http\Controllers\Admin\MedicalJustificationController as AdminMedicalJustificationController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\StudentTransferController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboardController;
use App\Http\Controllers\Finance\PaymentReceiptController as FinancePaymentReceiptController;
use App\Http\Controllers\Finance\StudentTuitionController;
use App\Http\Controllers\Finance\TuitionConfigController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parent\MedicalJustificationController;
use App\Http\Controllers\Parent\PaymentReceiptController as ParentPaymentReceiptController;
use App\Http\Controllers\Parent\PickupPersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\GradeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Messages routes
    Route::get('/messages', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::get('/api/messages/search', [MessageController::class, 'search'])->name('api.messages.search');

    // Rutas para Padres de Familia
    Route::middleware(['role:parent'])->prefix('parent')->name('parent.')->group(function () {
        Route::get('dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        Route::get('payment-receipts', [ParentPaymentReceiptController::class, 'index'])->name('payment-receipts.index');
        Route::get('payment-receipts/create', [ParentPaymentReceiptController::class, 'create'])->name('payment-receipts.create');
        Route::post('payment-receipts', [ParentPaymentReceiptController::class, 'store'])->name('payment-receipts.store');
        Route::get('payment-receipts/{paymentReceipt}', [ParentPaymentReceiptController::class, 'show'])->name('payment-receipts.show');

        Route::get('medical-justifications', [MedicalJustificationController::class, 'index'])->name('medical-justifications.index');
        Route::get('medical-justifications/create', [MedicalJustificationController::class, 'create'])->name('medical-justifications.create');
        Route::post('medical-justifications', [MedicalJustificationController::class, 'store'])->name('medical-justifications.store');

        Route::get('students/{student}/pickup-people', [PickupPersonController::class, 'index'])->name('pickup-people.index');
        Route::get('students/{student}/pickup-people/create', [PickupPersonController::class, 'create'])->name('pickup-people.create');
        Route::post('students/{student}/pickup-people', [PickupPersonController::class, 'store'])->name('pickup-people.store');
        Route::get('students/{student}/pickup-people/{pickupPerson}/edit', [PickupPersonController::class, 'edit'])->name('pickup-people.edit');
        Route::put('students/{student}/pickup-people/{pickupPerson}', [PickupPersonController::class, 'update'])->name('pickup-people.update');
        Route::delete('students/{student}/pickup-people/{pickupPerson}', [PickupPersonController::class, 'destroy'])->name('pickup-people.destroy');
    });

    // Rutas para Administrador de Finanzas
    Route::middleware(['role:admin,finance_admin'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard');

        Route::get('payment-receipts', [FinancePaymentReceiptController::class, 'index'])->name('payment-receipts.index');
        Route::get('payment-receipts/create', [FinancePaymentReceiptController::class, 'create'])->name('payment-receipts.create');
        Route::get('payment-receipts/student/{student}/parents', [FinancePaymentReceiptController::class, 'getStudentParents'])->name('payment-receipts.get-student-parents');
        Route::post('payment-receipts', [FinancePaymentReceiptController::class, 'store'])->name('payment-receipts.store');
        Route::get('payment-receipts/{paymentReceipt}', [FinancePaymentReceiptController::class, 'show'])->name('payment-receipts.show');
        Route::post('payment-receipts/{paymentReceipt}/update-status', [FinancePaymentReceiptController::class, 'updateStatus'])->name('payment-receipts.update-status');

        Route::resource('tuition-configs', TuitionConfigController::class)->except(['show']);

        Route::get('student-tuitions', [StudentTuitionController::class, 'index'])->name('student-tuitions.index');
        Route::get('student-tuitions/{studentTuition}/edit', [StudentTuitionController::class, 'edit'])->name('student-tuitions.edit');
        Route::put('student-tuitions/{studentTuition}', [StudentTuitionController::class, 'update'])->name('student-tuitions.update');
        Route::get('student-tuitions/discount-report', [StudentTuitionController::class, 'discountReport'])->name('student-tuitions.discount-report');
    });

    // Rutas para Maestros
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

        Route::resource('grades', GradeController::class);
        Route::resource('assignments', AssignmentController::class);
        Route::resource('announcements', AnnouncementController::class);

        Route::get('medical-justifications', [\App\Http\Controllers\Teacher\MedicalJustificationController::class, 'index'])->name('medical-justifications.index');
        Route::get('medical-justifications/{medicalJustification}', [\App\Http\Controllers\Teacher\MedicalJustificationController::class, 'show'])->name('medical-justifications.show');
        Route::post('medical-justifications/{medicalJustification}/approve', [\App\Http\Controllers\Teacher\MedicalJustificationController::class, 'approve'])->name('medical-justifications.approve');
        Route::post('medical-justifications/{medicalJustification}/reject', [\App\Http\Controllers\Teacher\MedicalJustificationController::class, 'reject'])->name('medical-justifications.reject');
    });

    // Rutas para Estudiantes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('schedule', [StudentDashboardController::class, 'schedule'])->name('schedule');
        Route::get('grades', [StudentDashboardController::class, 'grades'])->name('grades');
        Route::get('assignments', [StudentDashboardController::class, 'assignments'])->name('assignments');
    });

    // Rutas para Administradores
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::resource('users', UserController::class);
        Route::resource('school-years', SchoolYearController::class);
        Route::get('school-years/{schoolYear}/assign-students', [SchoolYearController::class, 'assignStudents'])->name('school-years.assign-students');
        Route::post('school-years/{schoolYear}/assign-students', [SchoolYearController::class, 'storeStudentAssignments'])->name('school-years.store-student-assignments');
        Route::resource('students', AdminStudentController::class);
        Route::get('students-transfer', [StudentTransferController::class, 'index'])->name('students.transfer');
        Route::get('students-transfer/get-students', [StudentTransferController::class, 'getStudents'])->name('students.get-students');
        Route::post('students-transfer', [StudentTransferController::class, 'transfer'])->name('students.transfer-store');
        Route::resource('subjects', SubjectController::class);
        Route::resource('schedules', ScheduleController::class);
        Route::get('schedules-visual', [ScheduleController::class, 'visual'])->name('schedules.visual');
        Route::get('schedules-visual/get-group-schedule', [ScheduleController::class, 'getGroupSchedule'])->name('schedules.get-group-schedule');
        Route::post('schedules-visual', [ScheduleController::class, 'storeVisual'])->name('schedules.store-visual');
        Route::put('schedules-visual/{schedule}', [ScheduleController::class, 'updateVisual'])->name('schedules.update-visual');
        Route::delete('schedules-visual/{schedule}', [ScheduleController::class, 'destroyVisual'])->name('schedules.destroy-visual');
        Route::get('schedules/copy/form', [ScheduleController::class, 'copyForm'])->name('schedules.copy-form');
        Route::post('schedules/copy', [ScheduleController::class, 'copy'])->name('schedules.copy');
        Route::get('grades', [GradeOverviewController::class, 'index'])->name('grades.index');
        Route::get('medical-justifications', [AdminMedicalJustificationController::class, 'index'])->name('medical-justifications.index');
        Route::get('medical-justifications/{medicalJustification}', [AdminMedicalJustificationController::class, 'show'])->name('medical-justifications.show');
        Route::post('medical-justifications/{medicalJustification}/approve', [AdminMedicalJustificationController::class, 'approve'])->name('medical-justifications.approve');
        Route::post('medical-justifications/{medicalJustification}/reject', [AdminMedicalJustificationController::class, 'reject'])->name('medical-justifications.reject');
    });

    // API routes for schedules
    Route::get('api/school-years/{schoolYear}/school-grades', [SchoolYearController::class, 'getSchoolGrades']);
});

require __DIR__.'/auth.php';
