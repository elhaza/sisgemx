<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PaymentHelper;
use App\Http\Controllers\Controller;
use App\Models\GradeSection;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use App\Models\User;
use App\PaymentType;
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
            $query->where('enrollment_number', 'like', '%'.addcslashes($request->enrollment_number, '%_').'%');
        }

        // Filter by name
        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.addcslashes($request->name, '%_').'%');
            });
        }

        // Filter by grade level (using school_grade_id)
        if ($request->filled('grade_level')) {
            $query->whereHas('schoolGrade', function ($q) use ($request) {
                $q->where('grade_level', $request->grade_level);
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
            $query->where('students.school_year_id', $request->school_year_id);
        } else {
            $activeSchoolYear = SchoolYear::where('is_active', true)->first();
            if ($activeSchoolYear) {
                $query->where('students.school_year_id', $activeSchoolYear->id);
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

        // Get per_page parameter from request (default to 15)
        $perPage = $request->get('per_page', 15);
        // Limit to allowed values to prevent abuse
        $allowedPerPage = [15, 30, 50, 100];
        if (! in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        // Handle sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Validate sort parameters
        $allowedSortColumns = [
            'enrollment_number' => 'students.enrollment_number',
            'name' => 'users.name',
            'gender' => 'students.gender',
            'level' => 'grade_sections.grade_level',
            'section' => 'grade_sections.section',
            'school_year' => 'school_years.name',
            'created_at' => 'students.created_at',
        ];

        if (! isset($allowedSortColumns[$sortBy])) {
            $sortBy = 'created_at';
        }

        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Apply sorting
        $columnToSort = $allowedSortColumns[$sortBy];

        // Handle joins for sorting related tables
        if (strpos($columnToSort, 'users.') !== false) {
            $query->join('users', 'students.user_id', '=', 'users.id');
        }
        if (strpos($columnToSort, 'grade_sections.') !== false) {
            $query->join('grade_sections', 'students.school_grade_id', '=', 'grade_sections.id');
        }
        if (strpos($columnToSort, 'school_years.') !== false) {
            $query->join('school_years', 'students.school_year_id', '=', 'school_years.id');
        }

        // Special handling for name sorting: sort by apellido_paterno, apellido_materno, then name
        if ($sortBy === 'name') {
            $query->orderBy('users.apellido_paterno', $sortOrder)
                ->orderBy('users.apellido_materno', $sortOrder)
                ->orderBy('users.name', $sortOrder);
        } else {
            $query->orderBy($columnToSort, $sortOrder);
        }

        // Select distinct to avoid duplicates from joins
        $query->select('students.*');

        $students = $query->paginate($perPage)->withQueryString();

        // Get data for filters
        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        // Get unique grade levels and groups for filters
        $gradeLevels = GradeSection::distinct('grade_level')->orderBy('grade_level')->pluck('grade_level');
        $groups = GradeSection::distinct('section')->orderBy('section')->pluck('section');

        return view('admin.students.index', compact('students', 'schoolYears', 'activeSchoolYear', 'gradeLevels', 'groups', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        $schoolYears = SchoolYear::all();
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        // Only get grades from active school year
        $schoolGrades = GradeSection::with('schoolYear')
            ->when($activeSchoolYear, function ($query) use ($activeSchoolYear) {
                $query->where('school_year_id', $activeSchoolYear->id);
            })
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get();

        $users = User::where('role', 'student')->whereDoesntHave('student')->get();
        $parents = User::where('role', 'parent')->get();

        return view('admin.students.create', compact('schoolYears', 'schoolGrades', 'users', 'parents', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        // Convert empty strings to null for nullable fields
        $request->merge([
            'tutor_2_id' => $request->input('tutor_2_id') ?: null,
            'billing_name' => $request->input('billing_name') ?: null,
            'billing_zip_code' => $request->input('billing_zip_code') ?: null,
            'billing_rfc' => $request->input('billing_rfc') ?: null,
            'billing_tax_regime' => $request->input('billing_tax_regime') ?: null,
            'billing_cfdi_use' => $request->input('billing_cfdi_use') ?: null,
            'discount_percentage' => $request->input('discount_percentage') ?: null,
        ]);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'school_year_id' => 'required|exists:school_years,id',
            'school_grade_id' => 'required|exists:grade_sections,id',
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
            $prefix = config('school.prefix_tuition');
            $schoolCode = config('school.school_code');
            $year = now()->year;

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

        // Apply discount to existing student tuitions for this school year (copy from students table)
        $discountPercentage = $validated['discount_percentage'] ?? 0;
        if ($discountPercentage > 0) {
            StudentTuition::where('student_id', $student->id)
                ->where('school_year_id', $validated['school_year_id'])
                ->update(['discount_percentage' => $discountPercentage]);
        }

        return redirect()->route('admin.students.index')->with('success', 'Estudiante inscrito exitosamente.');
    }

    public function show(Student $student)
    {
        $student->load([
            'user',
            'schoolYear',
            'schoolGrade',
            'tutor1',
            'tutor2',
            'tuitions' => function ($query) {
                $query->orderBy('year')->orderBy('month');
            },
        ]);

        // Calculate late fees for all unpaid tuitions
        foreach ($student->tuitions as $tuition) {
            if (! $tuition->isPaid() && $tuition->late_fee_amount == 0 && $tuition->days_late > 0) {
                $lateFee = PaymentHelper::calculateLateFee((float) $tuition->final_amount, $tuition->days_late);
                if ($lateFee > 0) {
                    $tuition->update(['late_fee_amount' => $lateFee]);
                }
            }
        }

        // Reload tuitions to reflect updated late fees
        $student->load('tuitions');

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $schoolYears = SchoolYear::all();
        $schoolGrades = GradeSection::with('schoolYear')->orderBy('school_year_id')->orderBy('grade_level')->orderBy('section')->get();
        $parents = User::where('role', 'parent')->get();

        return view('admin.students.edit', compact('student', 'schoolYears', 'schoolGrades', 'parents'));
    }

    public function update(Request $request, Student $student)
    {
        // Convert empty strings to null for nullable fields
        $request->merge([
            'tutor_2_id' => $request->input('tutor_2_id') ?: null,
            'billing_name' => $request->input('billing_name') ?: null,
            'billing_zip_code' => $request->input('billing_zip_code') ?: null,
            'billing_rfc' => $request->input('billing_rfc') ?: null,
            'billing_tax_regime' => $request->input('billing_tax_regime') ?: null,
            'billing_cfdi_use' => $request->input('billing_cfdi_use') ?: null,
            'discount_percentage' => $request->input('discount_percentage') ?: null,
        ]);

        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'school_grade_id' => 'required|exists:grade_sections,id',
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

        // Store old discount to detect if it changed
        $oldDiscount = $student->discount_percentage;
        $newDiscount = $validated['discount_percentage'] ?? 0;

        $student->update($validated);

        // If discount changed, update student_tuitions and pending payments for current and future months only
        if ((float) $oldDiscount !== (float) $newDiscount) {
            $this->updateDiscountForFutureMonths($student, $validated['school_year_id'], $newDiscount);
        }

        return redirect()->route('admin.students.index')->with('success', 'Estudiante actualizado exitosamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Estudiante eliminado exitosamente.');
    }

    public function updateLateFee(Request $request, Student $student, StudentTuition $studentTuition)
    {
        // Verify the tuition belongs to the student
        if ($studentTuition->student_id !== $student->id) {
            abort(404);
        }

        $validated = $request->validate([
            'late_fee_amount' => 'required|numeric|min:0|max:9999.99',
        ]);

        $oldLateFeeAmount = $studentTuition->late_fee_amount;
        $newLateFeeAmount = $validated['late_fee_amount'];

        // Update the late fee amount
        $studentTuition->update([
            'late_fee_amount' => $newLateFeeAmount,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Recargo actualizado de $'.number_format($oldLateFeeAmount, 2).' a $'.number_format($newLateFeeAmount, 2).' exitosamente.');
    }

    public function removeLateFee(Student $student, StudentTuition $studentTuition)
    {
        // Verify the tuition belongs to the student
        if ($studentTuition->student_id !== $student->id) {
            abort(404);
        }

        // Store the late fee amount for the message
        $lateFeeAmount = $studentTuition->late_fee_amount;

        // Remove the late fee by setting it to 0
        $studentTuition->update([
            'late_fee_amount' => 0,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Recargo de $'.number_format($lateFeeAmount, 2).' removido exitosamente.');
    }

    public function payTuition(Student $student, StudentTuition $studentTuition)
    {
        // Verify the tuition belongs to the student
        if ($studentTuition->student_id !== $student->id) {
            abort(404);
        }

        // Check if already paid
        if ($studentTuition->isPaid()) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'Esta mensualidad ya ha sido liquidada.');
        }

        // Check if there are any unpaid tuitions earlier than this one
        $earlierTuitions = StudentTuition::where('student_id', $student->id)
            ->where('school_year_id', $studentTuition->school_year_id)
            ->where(function ($query) use ($studentTuition) {
                $query->where('year', '<', $studentTuition->year)
                    ->orWhere(function ($q) use ($studentTuition) {
                        $q->where('year', $studentTuition->year)
                            ->where('month', '<', $studentTuition->month);
                    });
            })
            ->get();

        $hasEarlierUnpaidTuition = false;
        foreach ($earlierTuitions as $earlierTuition) {
            if (! $earlierTuition->isPaid()) {
                $hasEarlierUnpaidTuition = true;
                break;
            }
        }

        if ($hasEarlierUnpaidTuition) {
            return redirect()->route('admin.students.show', $student)
                ->with('error', 'No se puede liquidar esta mensualidad. Debe liquidar primero las mensualidades anteriores.');
        }

        $calculatedLateFee = $studentTuition->calculated_late_fee_amount;
        $totalAmount = $studentTuition->final_amount + $calculatedLateFee;

        // Create a payment record for this tuition
        Payment::create([
            'student_id' => $student->id,
            'student_tuition_id' => $studentTuition->id,
            'payment_type' => PaymentType::Tuition,
            'description' => $studentTuition->month_name.' '.$studentTuition->year,
            'amount' => $totalAmount,
            'month' => $studentTuition->month,
            'year' => $studentTuition->year,
            'due_date' => $studentTuition->due_date,
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        // Update late_fee_amount and late_fee_paid to track calculated fees
        $studentTuition->update([
            'late_fee_amount' => $calculatedLateFee,
            'late_fee_paid' => $calculatedLateFee,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Mensualidad de $'.number_format($totalAmount, 2).' liquidada exitosamente.');
    }

    /**
     * Update discount for student tuitions and payments for current and future months only
     */
    protected function updateDiscountForFutureMonths(Student $student, int $schoolYearId, float $newDiscount): void
    {
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;

        // Get all future tuitions (current month and onwards)
        $futureTuitions = StudentTuition::where('student_id', $student->id)
            ->where('school_year_id', $schoolYearId)
            ->where(function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', '>', $currentYear)
                    ->orWhere(function ($q) use ($currentYear, $currentMonth) {
                        $q->where('year', $currentYear)
                            ->where('month', '>=', $currentMonth);
                    });
            })
            ->get();

        // Update student_tuitions discount_percentage
        foreach ($futureTuitions as $tuition) {
            $tuition->update(['discount_percentage' => $newDiscount]);
        }

        // Update pending payments (is_paid = false) for these tuitions
        if ($futureTuitions->isNotEmpty()) {
            $tuitionIds = $futureTuitions->pluck('id');

            Payment::whereIn('student_tuition_id', $tuitionIds)
                ->where('is_paid', false)
                ->get()
                ->each(function (Payment $payment) use ($newDiscount) {
                    // Recalculate payment amount based on new discount
                    $tuition = $payment->studentTuition;
                    if ($tuition && $tuition->monthly_amount) {
                        $discountAmount = ($tuition->monthly_amount * $newDiscount) / 100;
                        $newAmount = $tuition->monthly_amount - $discountAmount;
                        $payment->update(['amount' => $newAmount]);
                    }
                });
        }
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

    public function getTuitionDetails(Student $student, StudentTuition $studentTuition): \Illuminate\Http\JsonResponse
    {
        // Verify the tuition belongs to the student
        if ($studentTuition->student_id !== $student->id) {
            abort(404);
        }

        $daysLate = PaymentHelper::calculateDaysLate($studentTuition->due_date->format('Y-m-d'));
        $gracePeriodDays = config('payment.grace_period_days', 0);

        // Calculate the late fee based on current configuration
        $calculatedLateFee = PaymentHelper::calculateLateFee((float) $studentTuition->final_amount, $daysLate);

        // Prepare late fee explanation
        $lateFeeExplanation = [];
        if ($daysLate > 0) {
            $lateFeeExplanation['days_late'] = $daysLate;
            // Only include grace period if it's greater than 0
            if ($gracePeriodDays > 0) {
                $lateFeeExplanation['grace_period_days'] = $gracePeriodDays;
            }
            $lateFeeExplanation['billable_days'] = max(0, $daysLate - $gracePeriodDays);

            $feeType = config('payment.late_fee_type', 'MONTHLY');
            $lateFeeExplanation['fee_type'] = $feeType;

            if ($feeType === 'ONCE') {
                $lateFeeExplanation['description'] = 'Se aplica una única vez como recargo fijo';
                $lateFeeExplanation['fee_amount'] = config('payment.late_fee_monthly_amount', 0);
            } elseif ($feeType === 'DAILY') {
                $dailyAmount = config('payment.late_fee_daily_amount', 0);
                $dailyRate = config('payment.late_fee_rate', 0);

                if ($dailyAmount > 0) {
                    $lateFeeExplanation['description'] = 'Recargo de $'.number_format($dailyAmount, 2).' por día';
                    $lateFeeExplanation['daily_fee'] = $dailyAmount;
                    $lateFeeExplanation['calculated_from'] = 'amount_per_day';
                } else {
                    $lateFeeExplanation['description'] = "Recargo de {$dailyRate}% de la mensualidad por día";
                    $lateFeeExplanation['daily_rate'] = $dailyRate;
                    $lateFeeExplanation['calculated_from'] = 'percentage';
                }
            } elseif ($feeType === 'MONTHLY') {
                $monthlyAmount = config('payment.late_fee_monthly_amount', 0);
                $monthlyRate = config('payment.late_fee_rate', 0);

                if ($monthlyAmount > 0) {
                    $lateFeeExplanation['description'] = 'Recargo de $'.number_format($monthlyAmount, 2).' por mes vencido';
                    $lateFeeExplanation['monthly_fee'] = $monthlyAmount;
                    $lateFeeExplanation['calculated_from'] = 'amount_per_month';
                } else {
                    $lateFeeExplanation['description'] = "Recargo de {$monthlyRate}% de la mensualidad por mes vencido";
                    $lateFeeExplanation['monthly_rate'] = $monthlyRate;
                    $lateFeeExplanation['calculated_from'] = 'percentage';
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tuition_id' => $studentTuition->id,
                'period' => $studentTuition->month_name.' '.$studentTuition->year,
                'student_name' => $student->user->name,
                'due_date' => $studentTuition->due_date->format('d/m/Y'),
                'days_late' => $daysLate,
                'is_paid' => $studentTuition->isPaid(),

                // Detalles de adeudo
                'debt_details' => [
                    'base_amount' => $studentTuition->monthly_amount,
                    'discount_percentage' => $studentTuition->discount_percentage,
                    'discount_amount' => $studentTuition->discount_amount,
                    'discount_reason' => $studentTuition->discount_reason,
                    'final_amount' => $studentTuition->final_amount,
                ],

                // Detalles de recargos
                'late_fee_details' => [
                    'amount' => $calculatedLateFee,
                    'paid_amount' => $studentTuition->late_fee_paid,
                    'remaining' => max(0, $calculatedLateFee - $studentTuition->late_fee_paid),
                    'explanation' => $lateFeeExplanation,
                ],

                // Total a pagar
                'payment_summary' => [
                    'base_tuition' => $studentTuition->final_amount,
                    'late_fees' => $calculatedLateFee,
                    'total_due' => $studentTuition->final_amount + $calculatedLateFee,
                    'amount_paid' => $studentTuition->late_fee_paid,
                    'amount_remaining' => $studentTuition->final_amount + max(0, $calculatedLateFee - $studentTuition->late_fee_paid),
                ],
            ],
        ]);
    }
}
