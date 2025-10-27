<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
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

            // Obtener anuncios vigentes
            $today = now()->toDateString();
            $allValidAnnouncements = Announcement::query()
                ->with('teacher')
                ->where(function ($query) use ($today) {
                    // Si no tienen fechas de vigencia, mostrar siempre
                    $query->whereNull('valid_from')
                        ->whereNull('valid_until')
                        // O si están dentro del rango de vigencia
                        ->orWhere(function ($q) use ($today) {
                            $q->where(function ($subQuery) use ($today) {
                                $subQuery->whereNull('valid_from')
                                    ->orWhere('valid_from', '<=', $today);
                            })
                                ->where(function ($subQuery) use ($today) {
                                    $subQuery->whereNull('valid_until')
                                        ->orWhere('valid_until', '>=', $today);
                                });
                        });
                })
                ->latest()
                ->get();

            // Mostrar solo los últimos 5 en el dashboard
            $recentAnnouncements = $allValidAnnouncements->take(5);
            $totalValidAnnouncements = $allValidAnnouncements->count();

            return view('dashboard', [
                'user' => $user,
                'activeSchoolYear' => $activeSchoolYear,
                'isOutOfRange' => $isOutOfRange,
                'unreadMessageCount' => $unreadMessageCount,
                'financialStats' => $financialStats,
                'recentAnnouncements' => $recentAnnouncements,
                'totalValidAnnouncements' => $totalValidAnnouncements,
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
        $allOverdueTuitions = StudentTuition::where('due_date', '<', $now->toDateString())
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        // Filter to only unpaid tuitions
        $overdueStudentIds = $allOverdueTuitions
            ->filter(function ($tuition) {
                return ! $tuition->isPaid();
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
            $allStudentOverdueTuitions = StudentTuition::where('student_id', $student->id)
                ->where('due_date', '<', $now->toDateString())
                ->get();

            $overdueTuitions = $allStudentOverdueTuitions
                ->filter(function ($tuition) {
                    return ! $tuition->isPaid();
                });

            $overdueCount = $overdueTuitions->count();
            $maxDaysLate = $overdueTuitions->max(function ($tuition) use ($now) {
                return $tuition->due_date ? abs((int) $tuition->due_date->diffInDays($now)) : 0;
            });

            // Calculate tuition amount and late fees
            $tuitionAmount = $overdueTuitions->sum('final_amount');
            $lateFeeAmount = $overdueTuitions->sum(function ($tuition) {
                return $tuition->late_fee;
            });
            $totalAmount = $tuitionAmount + $lateFeeAmount;

            // Format phone number for WhatsApp (add +52 if only 10 digits)
            $phoneNumber = $student->phone_number;
            if ($phoneNumber) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
                if (strlen($cleanPhone) === 10) {
                    $cleanPhone = '52'.$cleanPhone;
                }
                $phoneNumber = $cleanPhone;
            }

            // Add to tutor 1
            if ($student->tutor1) {
                $key = $student->tutor1->id;
                if ($parentReport->has($key)) {
                    $existing = $parentReport->get($key);
                    $existing['students']->push([
                        'id' => $student->id,
                        'name' => $student->user->full_name,
                        'phone_number' => $phoneNumber,
                        'overdue_count' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                        'tuition_amount' => $tuitionAmount,
                        'late_fee_amount' => $lateFeeAmount,
                        'total_amount' => $totalAmount,
                    ]);
                    $existing['total_overdue_tuitions'] += $overdueCount;
                    $existing['max_days_late'] = max($existing['max_days_late'], $maxDaysLate);
                    $existing['total_tuition_amount'] += $tuitionAmount;
                    $existing['total_late_fee_amount'] += $lateFeeAmount;
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
                                'phone_number' => $phoneNumber,
                                'overdue_count' => $overdueCount,
                                'max_days_late' => $maxDaysLate,
                                'tuition_amount' => $tuitionAmount,
                                'late_fee_amount' => $lateFeeAmount,
                                'total_amount' => $totalAmount,
                            ],
                        ]),
                        'total_overdue_tuitions' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                        'total_tuition_amount' => $tuitionAmount,
                        'total_late_fee_amount' => $lateFeeAmount,
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
                        'phone_number' => $phoneNumber,
                        'overdue_count' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                        'tuition_amount' => $tuitionAmount,
                        'late_fee_amount' => $lateFeeAmount,
                        'total_amount' => $totalAmount,
                    ]);
                    $existing['total_overdue_tuitions'] += $overdueCount;
                    $existing['max_days_late'] = max($existing['max_days_late'], $maxDaysLate);
                    $existing['total_tuition_amount'] += $tuitionAmount;
                    $existing['total_late_fee_amount'] += $lateFeeAmount;
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
                                'phone_number' => $phoneNumber,
                                'overdue_count' => $overdueCount,
                                'max_days_late' => $maxDaysLate,
                                'tuition_amount' => $tuitionAmount,
                                'late_fee_amount' => $lateFeeAmount,
                                'total_amount' => $totalAmount,
                            ],
                        ]),
                        'total_overdue_tuitions' => $overdueCount,
                        'max_days_late' => $maxDaysLate,
                        'total_tuition_amount' => $tuitionAmount,
                        'total_late_fee_amount' => $lateFeeAmount,
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
        $overdueTuitions = StudentTuition::where('due_date', '<', $now->toDateString())
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->filter(function ($tuition) {
                return ! $tuition->isPaid();
            });

        $parentsOverdue = Student::whereIn('id', $overdueTuitions->pluck('student_id'))
            ->distinct('user_id')
            ->count('user_id');

        // Colegiaturas pendientes de pago del mes
        $monthTuitions = StudentTuition::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->whereHas('student', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        $unpaidTuitionsMonth = $monthTuitions
            ->filter(function ($tuition) {
                return ! $tuition->isPaid();
            })
            ->sum('final_amount');

        // Calcular total de recargos por mora
        $lateFees = $overdueTuitions->sum('late_fee_amount');

        return [
            'monthly_payments' => $monthlyPayments,
            'pending_monthly_payments' => $pendingMonthlyPayments,
            'parents_overdue' => $parentsOverdue,
            'unpaid_tuitions_month' => $unpaidTuitionsMonth,
            'late_fees' => $lateFees,
        ];
    }
}
