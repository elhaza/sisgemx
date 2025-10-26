<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Redirigir según el rol del usuario
        if ($user->role->value === 'admin') {
            // Obtener el ciclo escolar activo
            $activeSchoolYear = SchoolYear::where('is_active', true)->first();

            // Verificar si está fuera del rango de fechas
            $isOutOfRange = false;
            if ($activeSchoolYear) {
                $today = now();
                $isOutOfRange = $today->lt($activeSchoolYear->start_date) || $today->gt($activeSchoolYear->end_date);
            }

            $unreadMessageCount = $user->unread_message_count;

            // Obtener estadísticas financieras
            $financialStats = $this->getFinancialStats();

            return view('dashboard', [
                'user' => $user,
                'activeSchoolYear' => $activeSchoolYear,
                'isOutOfRange' => $isOutOfRange,
                'unreadMessageCount' => $unreadMessageCount,
                'financialStats' => $financialStats,
            ]);
        }

        return match ($user->role->value) {
            'finance_admin' => redirect()->route('finance.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403),
        };
    }

    /**
     * Get overdue parents report
     */
    public function overdueParentsReport()
    {
        $now = now();

        // Get all overdue student tuitions with active students
        $overdueStudentIds = StudentTuition::where('due_date', '<', $now->toDateString())
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->pluck('student_id')
            ->unique();

        // Get students and their tutors
        $students = Student::whereIn('id', $overdueStudentIds)
            ->with(['user', 'tutor1', 'tutor2'])
            ->get();

        // Build report grouped by tutor
        $parentReport = collect();

        foreach ($students as $student) {
            $overdueTuitions = StudentTuition::where('student_id', $student->id)
                ->where('due_date', '<', $now->toDateString())
                ->get();

            $overdueCount = $overdueTuitions->count();
            $maxDaysLate = $overdueTuitions->max(function ($tuition) use ($now) {
                return $tuition->due_date ? abs((int) $tuition->due_date->diffInDays($now)) : 0;
            });

            // Add to tutor 1
            if ($student->tutor1) {
                $key = $student->tutor1->id;
                if ($parentReport->has($key)) {
                    $existing = $parentReport->get($key);
                    $existing['students']->push([
                        'id' => $student->id,
                        'name' => $student->user->full_name,
                        'phone_number' => $student->phone_number,
                        'overdue_count' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                    ]);
                    $existing['total_overdue_tuitions'] += $overdueCount;
                    $existing['max_days_late'] = max($existing['max_days_late'], $maxDaysLate);
                } else {
                    $parentReport->put($key, [
                        'tutor_1_name' => $student->tutor1->full_name,
                        'tutor_1_email' => $student->tutor1->email,
                        'tutor_2_name' => $student->tutor2?->full_name,
                        'tutor_2_email' => $student->tutor2?->email,
                        'students' => collect([
                            [
                                'id' => $student->id,
                                'name' => $student->user->full_name,
                                'phone_number' => $student->phone_number,
                                'overdue_count' => $overdueCount,
                                'max_days_late' => $maxDaysLate,
                            ],
                        ]),
                        'total_overdue_tuitions' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                    ]);
                }
            }

            // Add to tutor 2 if different from tutor 1
            if ($student->tutor2 && (! $student->tutor1 || $student->tutor2->id !== $student->tutor1->id)) {
                $key = $student->tutor2->id;
                if ($parentReport->has($key)) {
                    $existing = $parentReport->get($key);
                    $existing['students']->push([
                        'id' => $student->id,
                        'name' => $student->user->full_name,
                        'phone_number' => $student->phone_number,
                        'overdue_count' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                    ]);
                    $existing['total_overdue_tuitions'] += $overdueCount;
                    $existing['max_days_late'] = max($existing['max_days_late'], $maxDaysLate);
                } else {
                    $parentReport->put($key, [
                        'tutor_1_name' => $student->tutor2->full_name,
                        'tutor_1_email' => $student->tutor2->email,
                        'tutor_2_name' => null,
                        'tutor_2_email' => null,
                        'students' => collect([
                            [
                                'id' => $student->id,
                                'name' => $student->user->full_name,
                                'phone_number' => $student->phone_number,
                                'overdue_count' => $overdueCount,
                                'max_days_late' => $maxDaysLate,
                            ],
                        ]),
                        'total_overdue_tuitions' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                    ]);
                }
            }
        }

        return view('admin.overdue-parents-report', [
            'parentReport' => $parentReport->values(),
            'totalParents' => $parentReport->count(),
        ]);
    }

    /**
     * Get financial statistics for the current month
     */
    private function getFinancialStats(): array
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Pagos del mes (paid payments)
        $monthlyPayments = Payment::where('is_paid', true)
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('amount');

        // Pendientes de pago del mes (unpaid payments)
        $pendingMonthlyPayments = Payment::where('is_paid', false)
            ->whereMonth('month', $currentMonth)
            ->whereYear('year', $currentYear)
            ->sum('amount');

        // Cantidad de padres retrazados (parents with overdue tuitions)
        $parentsOverdue = StudentTuition::where('due_date', '<', $now->toDateString())
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->distinct('student_id')
            ->count('student_id');

        // Convertir a padres únicos
        $parentsOverdue = Student::whereIn('id',
            StudentTuition::where('due_date', '<', $now->toDateString())
                ->whereHas('student', function ($query) {
                    $query->where('status', 'active');
                })
                ->pluck('student_id')
        )->distinct('user_id')->count('user_id');

        // Colegiaturas pendientes de pago del mes
        $unpaidTuitionsMonth = StudentTuition::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->sum('final_amount');

        // Calcular total de recargos por mora
        $lateFees = StudentTuition::where('due_date', '<', $now->toDateString())
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->sum(function ($tuition) {
                return $tuition->late_fee;
            });

        return [
            'monthly_payments' => $monthlyPayments,
            'pending_monthly_payments' => $pendingMonthlyPayments,
            'parents_overdue' => $parentsOverdue,
            'unpaid_tuitions_month' => $unpaidTuitionsMonth,
            'late_fees' => $lateFees,
        ];
    }
}
