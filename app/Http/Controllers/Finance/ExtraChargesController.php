<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ChargeTemplate;
use App\Models\GradeSection;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentAssignedCharge;
use Illuminate\Http\Request;

class ExtraChargesController extends Controller
{
    /**
     * Display a listing of charge templates
     */
    public function index(): \Illuminate\View\View
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        $templates = ChargeTemplate::where('school_year_id', $activeSchoolYear?->id)
            ->with(['schoolYear', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('finance.extra-charges.index', [
            'templates' => $templates,
            'activeSchoolYear' => $activeSchoolYear,
        ]);
    }

    /**
     * Show the form for creating a new template
     */
    public function create(): \Illuminate\View\View
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $gradeSections = GradeSection::with('level')->orderBy('name')->get();

        return view('finance.extra-charges.create', [
            'activeSchoolYear' => $activeSchoolYear,
            'gradeSections' => $gradeSections,
        ]);
    }

    /**
     * Store a newly created charge template
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'charge_type' => 'required|string|in:inscription,materials,exam,other',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'default_due_date' => 'required|date|after:today',
            'apply_to' => 'required|in:all,grade_section,individual',
            'grade_section_ids' => 'nullable|array',
            'student_ids' => 'nullable|array',
        ]);

        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return back()->withErrors('No hay ciclo escolar activo');
        }

        $template = ChargeTemplate::create([
            'name' => $validated['name'],
            'charge_type' => $validated['charge_type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'default_due_date' => $validated['default_due_date'],
            'school_year_id' => $activeSchoolYear->id,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        // Assign to students based on apply_to
        $this->assignChargesToStudents($template, $validated);

        return redirect()->route('finance.extra-charges.index')
            ->with('success', "Cargo '{$template->name}' creado y asignado exitosamente");
    }

    /**
     * Show assigned charges for a specific template
     */
    public function show(ChargeTemplate $chargeTemplate): \Illuminate\View\View
    {
        $assignedCharges = StudentAssignedCharge::where('charge_template_id', $chargeTemplate->id)
            ->with(['student.user', 'student.schoolGrade', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('finance.extra-charges.show', [
            'template' => $chargeTemplate,
            'assignedCharges' => $assignedCharges,
        ]);
    }

    /**
     * Show the form for editing a template
     */
    public function edit(ChargeTemplate $chargeTemplate): \Illuminate\View\View
    {
        $gradeSections = GradeSection::with('level')->orderBy('name')->get();

        return view('finance.extra-charges.edit', [
            'template' => $chargeTemplate,
            'gradeSections' => $gradeSections,
        ]);
    }

    /**
     * Update a charge template
     */
    public function update(Request $request, ChargeTemplate $chargeTemplate): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'default_due_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $chargeTemplate->update($validated);

        return redirect()->route('finance.extra-charges.index')
            ->with('success', "Cargo '{$chargeTemplate->name}' actualizado exitosamente");
    }

    /**
     * Delete a charge template and its assignments
     */
    public function destroy(ChargeTemplate $chargeTemplate): \Illuminate\Http\RedirectResponse
    {
        $name = $chargeTemplate->name;
        $chargeTemplate->delete();

        return redirect()->route('finance.extra-charges.index')
            ->with('success', "Cargo '{$name}' eliminado exitosamente");
    }

    /**
     * Assign charges to students based on criteria
     */
    private function assignChargesToStudents(ChargeTemplate $template, array $data): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        $students = match ($data['apply_to']) {
            'all' => Student::where('school_year_id', $activeSchoolYear->id)
                ->where('status', 'active')
                ->pluck('id'),
            'grade_section' => Student::where('school_year_id', $activeSchoolYear->id)
                ->where('status', 'active')
                ->whereIn('grade_section_id', $data['grade_section_ids'] ?? [])
                ->pluck('id'),
            'individual' => $data['student_ids'] ?? [],
            default => [],
        };

        foreach ($students as $studentId) {
            StudentAssignedCharge::updateOrCreate(
                ['student_id' => $studentId, 'charge_template_id' => $template->id],
                [
                    'amount' => $template->amount,
                    'due_date' => $template->default_due_date,
                    'is_paid' => false,
                    'created_by' => auth()->id(),
                ]
            );
        }
    }

    /**
     * Bulk assign charge template to multiple students
     */
    public function bulkAssign(Request $request, ChargeTemplate $chargeTemplate): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'assign_to' => 'required|in:all,grade_section,individual',
            'grade_section_ids' => 'nullable|array',
            'student_ids' => 'nullable|array',
        ]);

        $this->assignChargesToStudents($chargeTemplate, $validated);

        return back()->with('success', 'Cargo asignado a los estudiantes seleccionados');
    }

    /**
     * Mark charge as paid
     */
    public function markAsPaid(StudentAssignedCharge $assignedCharge): \Illuminate\Http\RedirectResponse
    {
        $assignedCharge->update(['is_paid' => true]);

        return back()->with('success', 'Cargo marcado como pagado');
    }
}
