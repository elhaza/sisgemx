<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MedicalJustification;
use App\Models\Student;
use Illuminate\Http\Request;

class MedicalJustificationController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();

        // Get students that this teacher teaches
        $studentIds = Student::whereHas('schoolGrade.schedules.subject', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->pluck('id');

        $query = MedicalJustification::with(['student.user', 'student.schoolGrade', 'parent', 'approver'])
            ->whereIn('student_id', $studentIds);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('absence_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('absence_date', '<=', $request->to_date);
        }

        $medicalJustifications = $query->latest()->paginate(15);

        return view('teacher.medical-justifications.index', compact('medicalJustifications'));
    }

    public function show(MedicalJustification $medicalJustification)
    {
        $teacher = auth()->user();

        // Verify that the teacher teaches this student
        if (! $medicalJustification->student->hasTeacher($teacher->id)) {
            abort(403, 'No tienes permiso para ver este justificante.');
        }

        $medicalJustification->load(['student.user', 'student.schoolGrade', 'parent', 'approver']);

        return view('teacher.medical-justifications.show', compact('medicalJustification'));
    }

    public function approve(MedicalJustification $medicalJustification)
    {
        $teacher = auth()->user();

        // Verify that the teacher teaches this student
        if (! $medicalJustification->student->hasTeacher($teacher->id)) {
            abort(403, 'No tienes permiso para aprobar este justificante.');
        }

        if (! $medicalJustification->isPending()) {
            return redirect()->back()->with('error', 'Este justificante ya ha sido procesado.');
        }

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
        $teacher = auth()->user();

        // Verify that the teacher teaches this student
        if (! $medicalJustification->student->hasTeacher($teacher->id)) {
            abort(403, 'No tienes permiso para rechazar este justificante.');
        }

        if (! $medicalJustification->isPending()) {
            return redirect()->back()->with('error', 'Este justificante ya ha sido procesado.');
        }

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
