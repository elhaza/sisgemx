<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeSection;
use App\Models\SchoolYear;
use Illuminate\Http\Request;

class GradeSectionController extends Controller
{
    public function index()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $gradeSections = GradeSection::with('schoolYear')
            ->when($activeSchoolYear, function ($query) use ($activeSchoolYear) {
                $query->where('school_year_id', $activeSchoolYear->id);
            })
            ->orderBy('grade_level')
            ->orderBy('section')
            ->paginate(20);

        $schoolYears = SchoolYear::all();

        return view('admin.grade-sections.index', compact('gradeSections', 'schoolYears', 'activeSchoolYear'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::all();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $gradeLevels = [1, 2, 3, 4, 5, 6];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F'];

        return view('admin.grade-sections.create', compact('schoolYears', 'activeSchoolYear', 'gradeLevels', 'sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|integer|between:1,6',
            'section' => 'required|string|size:1|in:A,B,C,D,E,F',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        // Check if section already exists for this grade_level and school_year
        $exists = GradeSection::where('grade_level', $validated['grade_level'])
            ->where('section', $validated['section'])
            ->where('school_year_id', $validated['school_year_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe una sección con este grado y letra para este ciclo escolar.');
        }

        GradeSection::create($validated);

        return redirect()->route('admin.grade-sections.index')
            ->with('success', 'Sección de grado creada exitosamente.');
    }

    public function edit(GradeSection $gradeSection)
    {
        $schoolYears = SchoolYear::all();
        $gradeLevels = [1, 2, 3, 4, 5, 6];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F'];

        return view('admin.grade-sections.edit', compact('gradeSection', 'schoolYears', 'gradeLevels', 'sections'));
    }

    public function update(Request $request, GradeSection $gradeSection)
    {
        $validated = $request->validate([
            'grade_level' => 'required|integer|between:1,6',
            'section' => 'required|string|size:1|in:A,B,C,D,E,F',
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        // Check if section already exists for this grade_level and school_year (excluding current)
        $exists = GradeSection::where('grade_level', $validated['grade_level'])
            ->where('section', $validated['section'])
            ->where('school_year_id', $validated['school_year_id'])
            ->where('id', '!=', $gradeSection->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya existe otra sección con este grado y letra para este ciclo escolar.');
        }

        $gradeSection->update($validated);

        return redirect()->route('admin.grade-sections.index')
            ->with('success', 'Sección de grado actualizada exitosamente.');
    }

    public function destroy(GradeSection $gradeSection)
    {
        // Check if there are schedules in this section
        if ($gradeSection->schedules()->exists()) {
            return redirect()->route('admin.grade-sections.index')
                ->with('error', 'No se puede eliminar una sección que tiene horarios asignados.');
        }

        $gradeSection->delete();

        return redirect()->route('admin.grade-sections.index')
            ->with('success', 'Sección de grado eliminada exitosamente.');
    }

    /**
     * Get available grade sections for transferring students
     */
    public function getTransferOptions(GradeSection $gradeSection)
    {
        $studentCount = $gradeSection->students()->count();

        // Get other sections at the same grade level with student counts
        $availableSections = GradeSection::withCount('students')
            ->where('grade_level', $gradeSection->grade_level)
            ->where('school_year_id', $gradeSection->school_year_id)
            ->where('id', '!=', $gradeSection->id)
            ->orderBy('section')
            ->get()
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'section' => $section->section,
                    'students_count' => $section->students_count,
                ];
            });

        return response()->json([
            'gradeSection' => $gradeSection,
            'studentCount' => $studentCount,
            'availableSections' => $availableSections,
        ]);
    }

    /**
     * Transfer students to another section and delete the current one
     */
    public function transferAndDelete(Request $request, GradeSection $gradeSection)
    {
        $targetSectionId = $request->input('target_section_id');

        if (! $targetSectionId) {
            return redirect()->route('admin.grade-sections.index')
                ->with('error', 'Debe seleccionar una sección de destino.');
        }

        $targetSection = GradeSection::find($targetSectionId);
        if (! $targetSection) {
            return redirect()->route('admin.grade-sections.index')
                ->with('error', 'Sección de destino no encontrada.');
        }

        // Transfer all students
        $studentCount = $gradeSection->students()->count();
        $gradeSection->students()->update(['school_grade_id' => $targetSectionId]);

        // Delete the section
        $gradeSection->delete();

        return redirect()->route('admin.grade-sections.index')
            ->with('success', "Sección eliminada exitosamente. {$studentCount} estudiante(s) transferido(s) a {$targetSection->section}.");
    }
}
