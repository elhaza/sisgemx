<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolGrade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'schoolYear', 'schoolGrade', 'tuitions' => function ($query) {
            $query->where('discount_percentage', '>', 0)
                ->select('student_id', 'discount_percentage')
                ->distinct();
        }]);

        // Filter by status (default to active only)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, show only active students
            $query->where('status', 'active');
        }

        // Filter by enrollment number
        if ($request->filled('enrollment_number')) {
            $query->where('enrollment_number', 'like', '%'.$request->enrollment_number.'%');
        }

        // Filter by name
        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }

        // Filter by grade level (using school_grade_id)
        if ($request->filled('grade_level')) {
            $query->whereHas('schoolGrade', function ($q) use ($request) {
                $q->where('level', $request->grade_level);
            });
        }

        // Filter by group (using school_grade section)
        if ($request->filled('group')) {
            $query->whereHas('schoolGrade', function ($q) use ($request) {
                $q->where('section', $request->group);
            });
        }

        // Filter by school year (default to active)
        if ($request->filled('school_year_id')) {
            $query->where('school_year_id', $request->school_year_id);
        } else {
            $activeSchoolYear = SchoolYear::where('is_active', true)->first();
            if ($activeSchoolYear) {
                $query->where('school_year_id', $activeSchoolYear->id);
            }
        }

        // Filter by discount
        if ($request->filled('has_discount')) {
            if ($request->has_discount === 'yes') {
                $query->whereHas('tuitions', function ($q) {
                    $q->where('discount_percentage', '>', 0);
                });
            } elseif ($request->has_discount === 'no') {
                $query->whereDoesntHave('tuitions', function ($q) {
                    $q->where('discount_percentage', '>', 0);
                });
            }
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        // Get data for filters
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        return view('admin.students.index', compact('students', 'schoolYears', 'activeSchoolYear'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::all();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        // Only get grades from active school year
        $schoolGrades = SchoolGrade::with('schoolYear')
            ->when($activeSchoolYear, function ($query) use ($activeSchoolYear) {
                $query->where('school_year_id', $activeSchoolYear->id);
            })
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        $users = User::where('role', 'student')->whereDoesntHave('student')->get();
        $parents = User::where('role', 'parent')->get();

        return view('admin.students.create', compact('schoolYears', 'schoolGrades', 'users', 'parents', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'school_year_id' => 'required|exists:school_years,id',
            'school_grade_id' => 'required|exists:school_grades,id',
            'enrollment_number' => 'nullable|string|unique:students',
            'curp' => 'nullable|string|size:18|unique:students',
            'gender' => 'nullable|string|in:male,female,unspecified',
            'birth_country' => 'nullable|string|max:255',
            'birth_state' => 'nullable|string|max:255',
            'birth_city' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_email' => 'nullable|email|max:255',
            'tutor_1_id' => 'required|exists:users,id',
            'tutor_2_id' => 'nullable|exists:users,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'requires_invoice' => 'boolean',
            'billing_name' => 'nullable|string|max:255',
            'billing_zip_code' => 'nullable|string|max:10',
            'billing_rfc' => 'nullable|string|max:13',
            'billing_tax_regime' => 'nullable|string',
            'billing_cfdi_use' => 'nullable|string',
            'tax_certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Auto-generate enrollment number if not provided
        if (empty($validated['enrollment_number'])) {
            $prefix = env('PREFIX_TUITION', 'PRI');
            $schoolCode = env('SCHOOL_CODE', 'ESC001');
            $year = date('Y');

            // Get the next consecutive number
            $lastStudent = Student::orderBy('id', 'desc')->first();
            $consecutive = $lastStudent ? $lastStudent->id + 1 : 1;

            $validated['enrollment_number'] = sprintf('%s-%s-%s-%03d', $prefix, $year, $schoolCode, $consecutive);
        }

        // Handle file upload
        if ($request->hasFile('tax_certificate_file')) {
            $validated['tax_certificate_file'] = $request->file('tax_certificate_file')->store('tax_certificates', 'public');
        }

        // Extract date of birth from CURP if provided
        if (! empty($validated['curp']) && strlen($validated['curp']) === 18) {
            $dateOfBirth = $this->extractDateFromCurp($validated['curp']);
            if ($dateOfBirth) {
                $validated['date_of_birth'] = $dateOfBirth;
            }
        }

        $student = Student::create($validated);

        // Apply discount to existing student tuitions for this school year
        $discountPercentage = $validated['discount_percentage'] ?? 0;
        if ($discountPercentage > 0) {
            StudentTuition::where('student_id', $student->id)
                ->where('school_year_id', $validated['school_year_id'])
                ->update(['discount_percentage' => $discountPercentage]);
        }

        return redirect()->route('admin.students.index')->with('success', 'Estudiante inscrito exitosamente.');
    }

    public function edit(Student $student)
    {
        $schoolYears = SchoolYear::all();
        $schoolGrades = SchoolGrade::with('schoolYear')->orderBy('school_year_id')->orderBy('level')->orderBy('section')->get();
        $parents = User::where('role', 'parent')->get();

        return view('admin.students.edit', compact('student', 'schoolYears', 'schoolGrades', 'parents'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'school_grade_id' => 'required|exists:school_grades,id',
            'enrollment_number' => 'required|string|unique:students,enrollment_number,'.$student->id,
            'curp' => 'nullable|string|size:18|unique:students,curp,'.$student->id,
            'gender' => 'nullable|string|in:male,female,unspecified',
            'birth_country' => 'nullable|string|max:255',
            'birth_state' => 'nullable|string|max:255',
            'birth_city' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_email' => 'nullable|email|max:255',
            'tutor_1_id' => 'required|exists:users,id',
            'tutor_2_id' => 'nullable|exists:users,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'requires_invoice' => 'boolean',
            'billing_name' => 'nullable|string|max:255',
            'billing_zip_code' => 'nullable|string|max:10',
            'billing_rfc' => 'nullable|string|max:13',
            'billing_tax_regime' => 'nullable|string',
            'billing_cfdi_use' => 'nullable|string',
            'tax_certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('tax_certificate_file')) {
            $validated['tax_certificate_file'] = $request->file('tax_certificate_file')->store('tax_certificates', 'public');
        }

        // Extract date of birth from CURP if provided and changed
        if (! empty($validated['curp']) && strlen($validated['curp']) === 18 && $validated['curp'] !== $student->curp) {
            $dateOfBirth = $this->extractDateFromCurp($validated['curp']);
            if ($dateOfBirth) {
                $validated['date_of_birth'] = $dateOfBirth;
            }
        }

        $student->update($validated);

        // Update discount for student tuitions in this school year
        $discountPercentage = $validated['discount_percentage'] ?? 0;
        StudentTuition::where('student_id', $student->id)
            ->where('school_year_id', $validated['school_year_id'])
            ->update(['discount_percentage' => $discountPercentage]);

        return redirect()->route('admin.students.index')->with('success', 'Estudiante actualizado exitosamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Estudiante eliminado exitosamente.');
    }

    /**
     * Extract date of birth from CURP
     * CURP format: AAAA######HHHHHH##
     * Positions 4-9 contain YYMMDD (year, month, day)
     */
    protected function extractDateFromCurp(string $curp): ?string
    {
        if (strlen($curp) !== 18) {
            return null;
        }

        try {
            // Extract year, month, day from positions 4-9
            $year = substr($curp, 4, 2);
            $month = substr($curp, 6, 2);
            $day = substr($curp, 8, 2);

            // Determine century (before 2000 or after)
            $currentYear = date('y');
            $fullYear = (int) $year > (int) $currentYear ? '19'.$year : '20'.$year;

            // Validate date
            $date = $fullYear.'-'.$month.'-'.$day;
            if (! checkdate((int) $month, (int) $day, (int) $fullYear)) {
                return null;
            }

            return $date;
        } catch (\Exception $e) {
            return null;
        }
    }
}
