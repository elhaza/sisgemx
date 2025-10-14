<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Redirigir según el rol del usuario
        return match ($user->role->value) {
            'admin' => view('dashboard', ['user' => $user]),
            'finance_admin' => redirect()->route('finance.payment-receipts.index'),
            'teacher' => redirect()->route('teacher.announcements.index'),
            'parent' => redirect()->route('parent.payment-receipts.index'),
            'student' => redirect()->route('student.dashboard'),
            default => abort(403),
        };
    }
}
