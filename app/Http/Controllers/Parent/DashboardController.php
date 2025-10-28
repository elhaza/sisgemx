<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentTuition;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $unreadMessageCount = $user->unread_message_count;

        // Obtener los estudiantes del padre (como tutor 1 o tutor 2)
        $students = Student::where(function ($query) use ($user) {
            $query->where('tutor_1_id', $user->id)
                ->orWhere('tutor_2_id', $user->id);
        })->with(['pickupPeople', 'user', 'schoolGrade'])->get();

        // Obtener IDs de los estudiantes
        $studentIds = $students->pluck('id');

        // Obtener colegiaturas mensuales de todos los hijos
        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // Colegiaturas que deberían estar pagadas hasta ahora (incluyendo mes actual)
        $dueTuitions = StudentTuition::whereIn('student_id', $studentIds)
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', '<', $currentYear)
                    ->orWhere(function ($q) use ($currentYear, $currentMonth) {
                        $q->where('year', '=', $currentYear)
                            ->where('month', '<=', $currentMonth);
                    });
            })
            ->with(['student.user', 'monthlyTuition'])
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Obtener comprobantes aprobados para calcular colegiaturas pagadas
        $approvedReceipts = PaymentReceipt::whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->orderBy('payment_date')
            ->get();

        // Calcular total pagado de comprobantes aprobados
        $totalPaid = $approvedReceipts->sum('amount_paid');

        // Calcular cuántas colegiaturas se han cubierto con los pagos
        $remainingAmount = $totalPaid;
        $paidTuitionsCount = 0;
        $pendingTuitions = collect();
        $paidTuitions = collect();

        foreach ($dueTuitions as $tuition) {
            // Use total_amount which includes late fees
            $amountDue = $tuition->total_amount;
            if ($remainingAmount >= $amountDue) {
                $remainingAmount -= $amountDue;
                $paidTuitionsCount++;
                $paidTuitions->push($tuition);
            } else {
                $pendingTuitions->push($tuition);
            }
        }

        // Separar colegiaturas vencidas del mes actual
        $today = now()->startOfDay();
        $overdueTuitions = $pendingTuitions->filter(function ($tuition) use ($today) {
            return $tuition->due_date && $tuition->due_date->lt($today);
        });

        $currentMonthTuition = $pendingTuitions->filter(function ($tuition) use ($currentYear, $currentMonth) {
            return $tuition->year == $currentYear && $tuition->month == $currentMonth;
        })->first();

        // Total de colegiaturas que deberían estar pagadas hasta ahora
        $totalDueTuitions = $dueTuitions->count();

        // Comprobantes de pago recientes (últimos 10)
        $recentReceipts = PaymentReceipt::whereIn('student_id', $studentIds)
            ->with(['student.user'])
            ->latest()
            ->take(10)
            ->get();

        // Calcular monto total pendiente SOLO DE VENCIDOS (con recargos calculados dinámicamente)
        $totalOverdue = $overdueTuitions->sum('calculated_total_amount');

        // Calcular total de recargos en vencidos (usando valores calculados dinámicamente)
        $totalLateFees = $overdueTuitions->sum('calculated_late_fee_amount');

        // Unir vencidos + mes actual para la lista
        $displayPendingTuitions = $overdueTuitions;
        if ($currentMonthTuition) {
            $displayPendingTuitions = $displayPendingTuitions->push($currentMonthTuition);
        }

        return view('parent.dashboard', compact(
            'unreadMessageCount',
            'students',
            'displayPendingTuitions',
            'paidTuitions',
            'paidTuitionsCount',
            'totalDueTuitions',
            'recentReceipts',
            'totalOverdue',
            'totalLateFees',
            'overdueTuitions'
        ));
    }
}
