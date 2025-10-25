<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
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

            return view('dashboard', [
                'user' => $user,
                'activeSchoolYear' => $activeSchoolYear,
                'isOutOfRange' => $isOutOfRange,
                'unreadMessageCount' => $unreadMessageCount,
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
}
