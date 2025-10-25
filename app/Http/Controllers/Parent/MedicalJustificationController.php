<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\MedicalJustification;
use App\Models\Student;
use Illuminate\Http\Request;

class MedicalJustificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $medicalJustifications = MedicalJustification::where('parent_id', $user->id)
            ->with(['student.user'])
            ->latest()
            ->paginate(15);

        return view('parent.medical-justifications.index', compact('medicalJustifications'));
    }

    public function create()
    {
        // Get students related to this parent
        $students = Student::where(function ($query) {
            $query->where('tutor_1_id', auth()->id())
                ->orWhere('tutor_2_id', auth()->id());
        })->with('user')->get();

        return view('parent.medical-justifications.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'absence_date' => 'required|date',
            'reason' => 'required|string|max:1000',
            'document_file_path' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Store the document
        $filePath = $request->file('document_file_path')->store('medical-justifications', 'public');

        $validated['document_file_path'] = $filePath;
        $validated['parent_id'] = auth()->id();
        $validated['status'] = 'pending';

        MedicalJustification::create($validated);

        return redirect()->route('parent.medical-justifications.index')
            ->with('success', 'Justificante médico enviado exitosamente. Pendiente de aprobación.');
    }
}
