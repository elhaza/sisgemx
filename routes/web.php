<?php

use App\Http\Controllers\Admin\DatabaseBackupController;
use App\Http\Controllers\Admin\GradeOverviewController;
use App\Http\Controllers\Admin\GradeSectionController;
use App\Http\Controllers\Admin\MedicalJustificationController as AdminMedicalJustificationController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\StudentTransferController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnnouncementController as PublicAnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboardController;
use App\Http\Controllers\Finance\ExtraChargesController;
use App\Http\Controllers\Finance\PaymentReceiptController as FinancePaymentReceiptController;
use App\Http\Controllers\Finance\PaymentReportController;
use App\Http\Controllers\Finance\StudentTuitionController;
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
    Route::get('/admin/overdue-parents-report', [DashboardController::class, 'overdueParentsReport'])->middleware('role:admin')->name('admin.overdue-parents-report');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Announcements routes
    Route::get('/announcements', [PublicAnnouncementController::class, 'index'])->name('announcements.index');

    // Messages routes
    Route::get('/messages', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/messages/{message}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.mark-as-read');
    Route::post('/messages/{message}/mark-as-unread', [MessageController::class, 'markAsUnread'])->name('messages.mark-as-unread');
    Route::delete('/messages/{message}', [MessageController::class, 'delete'])->name('messages.delete');
    Route::get('/api/messages/search', [MessageController::class, 'search'])->name('api.messages.search');
    Route::get('/api/messages/student-teachers', [MessageController::class, 'getStudentTeachers'])->name('api.messages.student-teachers');
    Route::get('/api/messages/parent-teachers', [MessageController::class, 'getParentTeachers'])->name('api.messages.parent-teachers');

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
        Route::get('payment-receipts/export/excel', [FinancePaymentReceiptController::class, 'exportExcel'])->name('payment-receipts.export-excel');
        Route::get('payment-receipts/export/pdf', [FinancePaymentReceiptController::class, 'exportPdf'])->name('payment-receipts.export-pdf');
        Route::get('payment-receipts/create', [FinancePaymentReceiptController::class, 'create'])->name('payment-receipts.create');
        Route::get('payment-receipts/student/{student}/parents', [FinancePaymentReceiptController::class, 'getStudentParents'])->name('payment-receipts.get-student-parents');
        Route::post('payment-receipts', [FinancePaymentReceiptController::class, 'store'])->name('payment-receipts.store');
        Route::post('payment-receipts/bulk/update-status', [FinancePaymentReceiptController::class, 'bulkUpdateStatus'])->name('payment-receipts.bulk-update-status');
        Route::get('payment-receipts/{paymentReceipt}', [FinancePaymentReceiptController::class, 'show'])->name('payment-receipts.show');
        Route::post('payment-receipts/{paymentReceipt}/update-status', [FinancePaymentReceiptController::class, 'updateStatus'])->name('payment-receipts.update-status');

        Route::get('student-tuitions', [StudentTuitionController::class, 'index'])->name('student-tuitions.index');
        Route::get('student-tuitions/{studentTuition}/edit', [StudentTuitionController::class, 'edit'])->name('student-tuitions.edit');
        Route::put('student-tuitions/{studentTuition}', [StudentTuitionController::class, 'update'])->name('student-tuitions.update');
        Route::get('student-tuitions/discount-report', [StudentTuitionController::class, 'discountReport'])->name('student-tuitions.discount-report');

        Route::get('extra-charges', [ExtraChargesController::class, 'index'])->name('extra-charges.index');
        Route::get('extra-charges/create', [ExtraChargesController::class, 'create'])->name('extra-charges.create');
        Route::post('extra-charges', [ExtraChargesController::class, 'store'])->name('extra-charges.store');
        Route::get('extra-charges/{chargeTemplate}', [ExtraChargesController::class, 'show'])->name('extra-charges.show');
        Route::get('extra-charges/{chargeTemplate}/edit', [ExtraChargesController::class, 'edit'])->name('extra-charges.edit');
        Route::put('extra-charges/{chargeTemplate}', [ExtraChargesController::class, 'update'])->name('extra-charges.update');
        Route::delete('extra-charges/{chargeTemplate}', [ExtraChargesController::class, 'destroy'])->name('extra-charges.destroy');
        Route::post('extra-charges/{chargeTemplate}/bulk-assign', [ExtraChargesController::class, 'bulkAssign'])->name('extra-charges.bulk-assign');
        Route::post('assigned-charges/{assignedCharge}/mark-as-paid', [ExtraChargesController::class, 'markAsPaid'])->name('assigned-charges.mark-as-paid');

        Route::get('payment-reports/consolidated', [PaymentReportController::class, 'consolidatedPayments'])->name('payment-reports.consolidated');
    });

    // Rutas para Maestros
    Route::middleware(['role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

        Route::get('subjects/{subject}/students', [\App\Http\Controllers\Teacher\SubjectStudentsController::class, 'show'])->name('subject.students');

        Route::resource('grades', GradeController::class);
        Route::get('grades/api/students/{subject}', [GradeController::class, 'getStudentsBySubject'])->name('grades.api.students');
        Route::get('grades/bulk/{subject}', [GradeController::class, 'bulkGradeView'])->name('grades.bulk');
        Route::post('grades/bulk/store', [GradeController::class, 'storeBulkGrades'])->name('grades.bulk.store');
        Route::resource('assignments', AssignmentController::class);
        Route::get('assignments/{assignment}/download', [AssignmentController::class, 'download'])->name('assignments.download');
    });

    // Rutas para Anuncios (Teachers y Admins)
    Route::middleware(['role:teacher,admin'])->prefix('teacher')->name('teacher.')->group(function () {
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
        // Teacher availability routes
        Route::post('users/{user}/availabilities', [UserController::class, 'storeAvailability'])->name('users.store-availability');
        Route::delete('users/{user}/availabilities/{availabilityId}', [UserController::class, 'deleteAvailability'])->name('users.delete-availability');

        Route::resource('grade-sections', GradeSectionController::class);
        Route::get('grade-sections/{gradeSection}/transfer-options', [GradeSectionController::class, 'getTransferOptions'])->name('grade-sections.transfer-options');
        Route::post('grade-sections/{gradeSection}/transfer-and-delete', [GradeSectionController::class, 'transferAndDelete'])->name('grade-sections.transfer-and-delete');

        Route::resource('school-years', SchoolYearController::class);
        Route::get('school-years/{schoolYear}/assign-students', [SchoolYearController::class, 'assignStudents'])->name('school-years.assign-students');
        Route::post('school-years/{schoolYear}/assign-students', [SchoolYearController::class, 'storeStudentAssignments'])->name('school-years.store-student-assignments');
        // Student import routes (must be before resource routes to avoid parameter conflict)
        Route::get('students/import', [\App\Http\Controllers\Admin\StudentImportController::class, 'show'])->name('students.import');
        Route::post('students/import', [\App\Http\Controllers\Admin\StudentImportController::class, 'upload'])->name('students.import-upload');
        Route::get('students/create-school-year', [\App\Http\Controllers\Admin\StudentImportController::class, 'createSchoolYear'])->name('students.create-school-year');
        Route::post('students/create-school-year', [\App\Http\Controllers\Admin\StudentImportController::class, 'storeSchoolYear'])->name('students.store-school-year');

        // Student resource and transfer routes
        Route::resource('students', AdminStudentController::class);
        Route::get('students/{student}/tuitions/{studentTuition}/details', [AdminStudentController::class, 'getTuitionDetails'])->name('students.tuition-details');
        Route::patch('students/{student}/tuitions/{studentTuition}/update-late-fee', [AdminStudentController::class, 'updateLateFee'])->name('students.update-late-fee');
        Route::delete('students/{student}/tuitions/{studentTuition}/remove-late-fee', [AdminStudentController::class, 'removeLateFee'])->name('students.remove-late-fee');
        Route::post('students/{student}/tuitions/{studentTuition}/pay', [AdminStudentController::class, 'payTuition'])->name('students.pay-tuition');
        Route::get('students-transfer', [StudentTransferController::class, 'index'])->name('students.transfer');
        Route::get('students-transfer/get-students', [StudentTransferController::class, 'getStudents'])->name('students.get-students');
        Route::get('students-transfer/get-destination-grades', [StudentTransferController::class, 'getDestinationGrades'])->name('students.get-destination-grades');
        Route::post('students-transfer', [StudentTransferController::class, 'transfer'])->name('students.transfer-store');
        Route::resource('subjects', SubjectController::class);
        Route::post('subjects/teacher/store', [SubjectController::class, 'storeTeacher'])->name('subjects.store-teacher');
        Route::post('subjects/catalog/store', [SubjectController::class, 'storeCatalogSubject'])->name('subjects.store-catalog');
        Route::resource('schedules', ScheduleController::class);
        Route::get('schedules-visual', [ScheduleController::class, 'visual'])->name('schedules.visual');
        Route::get('schedules-visual/get-group-schedule', [ScheduleController::class, 'getGroupSchedule'])->name('schedules.get-group-schedule');
        Route::get('schedules-visual/get-available-teachers', [ScheduleController::class, 'getAvailableTeachers'])->name('schedules.get-available-teachers');
        Route::get('schedules-visual/get-group-students', [ScheduleController::class, 'getGroupStudents'])->name('schedules.get-group-students');
        Route::post('schedules-visual', [ScheduleController::class, 'storeVisual'])->name('schedules.store-visual');
        Route::put('schedules-visual/{schedule}', [ScheduleController::class, 'updateVisual'])->name('schedules.update-visual');
        Route::delete('schedules-visual/{schedule}', [ScheduleController::class, 'destroyVisual'])->name('schedules.destroy-visual');
        Route::get('schedules/copy/form', [ScheduleController::class, 'copyForm'])->name('schedules.copy-form');
        Route::post('schedules/copy', [ScheduleController::class, 'copy'])->name('schedules.copy');
        Route::get('schedules/generate/form', [ScheduleController::class, 'generateForm'])->name('schedules.generate-form');
        Route::post('schedules/generate', [ScheduleController::class, 'generate'])->name('schedules.generate');
        Route::post('schedules/confirm', [ScheduleController::class, 'confirm'])->name('schedules.confirm');

        // Schedule configuration resources
        Route::resource('time-slots', \App\Http\Controllers\Admin\TimeSlotController::class);
        Route::resource('teacher-subjects', \App\Http\Controllers\Admin\TeacherSubjectController::class);
        Route::get('grades', [GradeOverviewController::class, 'index'])->name('grades.index');
        Route::get('medical-justifications', [AdminMedicalJustificationController::class, 'index'])->name('medical-justifications.index');
        Route::get('medical-justifications/{medicalJustification}', [AdminMedicalJustificationController::class, 'show'])->name('medical-justifications.show');
        Route::post('medical-justifications/{medicalJustification}/approve', [AdminMedicalJustificationController::class, 'approve'])->name('medical-justifications.approve');
        Route::post('medical-justifications/{medicalJustification}/reject', [AdminMedicalJustificationController::class, 'reject'])->name('medical-justifications.reject');

        // Database operations routes
        Route::get('database/backup', [DatabaseBackupController::class, 'backup'])->name('database.backup');
        Route::get('database/restore', [DatabaseBackupController::class, 'showRestore'])->name('database.restore-form');
        Route::post('database/restore', [DatabaseBackupController::class, 'restore'])->name('database.restore');
        Route::get('database/clear-all', [DatabaseBackupController::class, 'showClearAll'])->name('database.clear-form');
        Route::post('database/clear-all', [DatabaseBackupController::class, 'clearAll'])->name('database.clear');
    });

    // API routes for schedules
    Route::get('api/school-years/{schoolYear}/school-grades', [SchoolYearController::class, 'getSchoolGrades']);

    // API routes for message filters
    Route::get('api/messages/filter-options', [\App\Http\Controllers\Api\MessageFilterController::class, 'getFilterOptions']);
    Route::get('api/messages/filter-data', [\App\Http\Controllers\Api\MessageFilterController::class, 'getFilterData']);
    Route::get('api/messages/users', [\App\Http\Controllers\Api\MessageFilterController::class, 'getUsers']);
});

require __DIR__.'/auth.php';
