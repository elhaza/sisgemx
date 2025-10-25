<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\PaymentReceiptStatusLog;
use App\Models\Student;
use App\Models\StudentTuition;
use App\ReceiptStatus;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $receipts = PaymentReceipt::query()
            ->where('parent_id', $user->id)
            ->with(['student.user', 'registeredBy'])
            ->latest()
            ->paginate(15);

        $children = Student::whereHas('user', function ($query) use ($user) {
            $query->where('parent_id', $user->id);
        })->with('user')->get();

        if ($children->isEmpty() && $user->student) {
            $children = collect([$user->student]);
        }

        $pendingPaymentsCount = \App\Models\Payment::whereHas('student', function ($query) use ($children) {
            $query->whereIn('id', $children->pluck('id'));
        })->where('is_paid', false)->count();

        $pendingReceiptsCount = PaymentReceipt::where('parent_id', $user->id)
            ->where('status', ReceiptStatus::Pending)
            ->count();

        $validatedReceiptsCount = PaymentReceipt::where('parent_id', $user->id)
            ->where('status', ReceiptStatus::Validated)
            ->count();

        $rejectedReceiptsCount = PaymentReceipt::where('parent_id', $user->id)
            ->where('status', ReceiptStatus::Rejected)
            ->count();

        $medicalJustificationsCount = \App\Models\MedicalJustification::where('parent_id', $user->id)->count();

        return view('parent.payment-receipts.index', compact(
            'receipts',
            'children',
            'pendingPaymentsCount',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'rejectedReceiptsCount',
            'medicalJustificationsCount'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        // Get students related to this parent
        $students = Student::where(function ($query) use ($user) {
            $query->where('tutor_1_id', $user->id)
                ->orWhere('tutor_2_id', $user->id);
        })->with('user')->get();

        // If parent doesn't have children, check if they themselves are a student
        if ($students->isEmpty() && $user->student) {
            $students = collect([$user->student]);
        }

        $currentDate = now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;

        // Get pending tuitions for each student
        $pendingTuitionsByStudent = [];

        foreach ($students as $student) {
            // Get all tuitions due up to current month
            $dueTuitions = StudentTuition::where('student_id', $student->id)
                ->where(function ($query) use ($currentYear, $currentMonth) {
                    $query->where('year', '<', $currentYear)
                        ->orWhere(function ($q) use ($currentYear, $currentMonth) {
                            $q->where('year', '=', $currentYear)
                                ->where('month', '<=', $currentMonth);
                        });
                })
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Get approved receipts for this student
            $approvedReceipts = PaymentReceipt::where('student_id', $student->id)
                ->where('status', 'approved')
                ->orderBy('payment_date')
                ->get();

            $totalPaid = $approvedReceipts->sum('amount_paid');

            // Calculate which tuitions are still pending
            $remainingAmount = $totalPaid;
            $pendingTuitions = collect();

            foreach ($dueTuitions as $tuition) {
                $amountDue = $tuition->total_amount;
                if ($remainingAmount >= $amountDue) {
                    $remainingAmount -= $amountDue;
                } else {
                    $pendingTuitions->push($tuition);
                }
            }

            $pendingTuitionsByStudent[$student->id] = $pendingTuitions;
        }

        return view('parent.payment-receipts.create', compact('students', 'pendingTuitionsByStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'payment_year' => 'nullable|integer|min:2020|max:2100',
            'payment_month' => 'nullable|integer|min:1|max:12',
            'reference' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'issuing_bank' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,transfer,card,check',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'receipt_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle both receipt_image and receipt_file fields
        $fileField = $request->hasFile('receipt_file') ? 'receipt_file' : 'receipt_image';

        if ($request->hasFile($fileField)) {
            $imagePath = $request->file($fileField)->store('payment-receipts', 'public');
            $validated['receipt_image'] = $imagePath;
        }

        // Set defaults for optional fields if coming from dashboard modal
        $validated['reference'] = $validated['reference'] ?? 'N/A';
        $validated['account_holder_name'] = $validated['account_holder_name'] ?? auth()->user()->full_name;
        $validated['issuing_bank'] = $validated['issuing_bank'] ?? 'No especificado';
        $validated['payment_method'] = $validated['payment_method'] ?? 'transfer';

        $validated['parent_id'] = auth()->id();
        $validated['registered_by_id'] = auth()->id();
        $validated['status'] = ReceiptStatus::Pending;

        $receipt = PaymentReceipt::create($validated);

        // Log the status creation
        $notesText = 'Comprobante creado por el padre/tutor';
        if (! empty($validated['payment_year']) && ! empty($validated['payment_month'])) {
            $months = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
            ];
            $monthName = $months[$validated['payment_month']] ?? '';
            $notesText .= " - Pago correspondiente a {$monthName} {$validated['payment_year']}";
        }

        PaymentReceiptStatusLog::create([
            'payment_receipt_id' => $receipt->id,
            'changed_by_id' => auth()->id(),
            'previous_status' => null,
            'new_status' => ReceiptStatus::Pending,
            'notes' => $notesText,
        ]);

        return redirect()->route('parent.payment-receipts.index')
            ->with('success', 'Comprobante de pago enviado exitosamente. Pendiente de validación.');
    }

    public function show(PaymentReceipt $paymentReceipt)
    {
        // Ensure parent can only view their own receipts
        if ($paymentReceipt->parent_id !== auth()->id()) {
            abort(403);
        }

        $paymentReceipt->load(['student.user', 'parent', 'registeredBy', 'validatedBy', 'statusLogs.changedBy']);

        return view('parent.payment-receipts.show', compact('paymentReceipt'));
    }
}
