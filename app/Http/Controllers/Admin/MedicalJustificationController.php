<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalJustification;
use Illuminate\Http\Request;

class MedicalJustificationController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalJustification::with(['student.user', 'parent']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('absence_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('absence_date', '<=', $request->to_date);
        }

        $medicalJustifications = $query->latest()->paginate(15);

        return view('admin.medical-justifications.index', compact('medicalJustifications'));
    }

    public function show(MedicalJustification $medicalJustification)
    {
        $medicalJustification->load(['student.user', 'parent', 'approver']);

        return view('admin.medical-justifications.show', compact('medicalJustification'));
    }

    public function approve(MedicalJustification $medicalJustification)
    {
        $medicalJustification->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Justificante médico aprobado exitosamente.');
    }

    public function reject(Request $request, MedicalJustification $medicalJustification)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $medicalJustification->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Justificante médico rechazado.');
    }
}
