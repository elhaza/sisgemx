<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\PaymentReceiptStatusLog;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\ReceiptStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $month = $request->get('month');
        $year = $request->get('year');
        $view = $request->get('view', 'month'); // 'month' or 'school_year'

        // Get active school year for context
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        // Determine the year to use
        $displayYear = $year ?: ($activeSchoolYear ? $activeSchoolYear->end_date->year : now()->year);

        // Determine month range for display
        $monthLabel = 'Ciclo Escolar Completo';
        $schoolYearMonths = collect();

        if ($activeSchoolYear) {
            // Calculate months in school year
            $startMonth = $activeSchoolYear->start_date->month;
            $startYear = $activeSchoolYear->start_date->year;
            $endMonth = $activeSchoolYear->end_date->month;
            $endYear = $activeSchoolYear->end_date->year;

            for ($y = $startYear; $y <= $endYear; $y++) {
                $monthStart = ($y === $startYear) ? $startMonth : 1;
                $monthEnd = ($y === $endYear) ? $endMonth : 12;
                for ($m = $monthStart; $m <= $monthEnd; $m++) {
                    $schoolYearMonths->push(['month' => $m, 'year' => $y]);
                }
            }
        }

        // Get PaymentReceipts
        $query = PaymentReceipt::query()
            ->with(['student.user', 'parent', 'registeredBy']);

        if ($status) {
            $query->where('status', $status);
        }

        // Handle month view
        if ($view === 'month') {
            $monthToDisplay = $month ?: now()->month;
            $yearToDisplay = $year ?: now()->year;
            $query->whereMonth('payment_date', $monthToDisplay)
                ->whereYear('payment_date', $yearToDisplay);

            $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $monthLabel = $monthNames[(int) $monthToDisplay].' '.$yearToDisplay;
        } elseif ($activeSchoolYear && $view === 'school_year') {
            // Filter by school year months
            $query->whereBetween('payment_date', [
                $activeSchoolYear->start_date,
                $activeSchoolYear->end_date,
            ]);
        }

        $receipts = $query->latest()->paginate(15)->withQueryString();

        // Get Payments registered by admin
        $paymentQuery = Payment::query()
            ->with(['student.user', 'studentTuition'])
            ->where('is_paid', true);

        if ($view === 'month') {
            $monthToDisplay = $month ?: now()->month;
            $yearToDisplay = $year ?: now()->year;
            $paymentQuery->where('month', $monthToDisplay)
                ->where('year', $yearToDisplay);
        } elseif ($activeSchoolYear && $view === 'school_year') {
            // Get all payments in school year months
            $paymentQuery->whereIn(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'),
                $schoolYearMonths->map(function ($m) {
                    return sprintf('%04d-%02d', $m['year'], $m['month']);
                })->toArray()
            );
        }

        $adminPayments = $paymentQuery->get();

        // Transform admin payments to match PaymentReceipt structure
        $adminPaymentReceipts = $adminPayments->map(function ($payment) {
            return (object) [
                'id' => 'admin-'.$payment->id,
                'type' => 'admin_payment',
                'payment_id' => $payment->id,
                'payment_date' => $payment->paid_at,
                'student' => $payment->student,
                'parent' => $payment->student->tutor1,
                'amount_paid' => $payment->amount,
                'status' => ReceiptStatus::Validated,
                'receipt_image' => null,
                'receipt_image_url' => null,
                'description' => $payment->description,
            ];
        });

        // If status filter is 'validated', include admin payments in the list
        $receiptItems = [];
        if (! $status || $status === 'validated') {
            // Get all receipt items and add image URLs
            foreach ($receipts->items() as $receipt) {
                $receipt->receipt_image_url = $receipt->receipt_image ? Storage::url($receipt->receipt_image) : null;
                $receiptItems[] = $receipt;
            }

            // Merge with admin payments
            $mergedReceipts = array_merge($receiptItems, $adminPaymentReceipts->toArray());

            // Sort by date descending
            usort($mergedReceipts, function ($a, $b) {
                $dateA = $a->payment_date ?? $a->created_at ?? now();
                $dateB = $b->payment_date ?? $b->created_at ?? now();

                return $dateB->getTimestamp() - $dateA->getTimestamp();
            });

            // Create a paginated collection
            $page = $request->input('page', 1);
            $perPage = 15;
            $offset = ($page - 1) * $perPage;
            $receiptItems = array_slice($mergedReceipts, $offset, $perPage);
            $totalRecords = count($mergedReceipts);
        } else {
            foreach ($receipts->items() as $receipt) {
                $receipt->receipt_image_url = $receipt->receipt_image ? Storage::url($receipt->receipt_image) : null;
                $receiptItems[] = $receipt;
            }
            $totalRecords = count($receiptItems);
        }

        // Use receiptItems instead of receipts for the view
        $receipts = $receiptItems;

        $pendingReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Pending)->count();
        $validatedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Validated)->count() + $adminPayments->count();

        // Calculate income based on view
        $monthToCalculate = $month ?: now()->month;
        $yearToCalculate = $year ?: now()->year;

        $incomeCurrentMonth = 0;
        $incomeAccumulated = 0;
        $incomeMonthlyDetails = collect();
        $incomeLabel = 'Ingresos';

        if ($view === 'month') {
            // Calculate income for the selected month
            $incomeCurrentMonth = PaymentReceipt::where('status', ReceiptStatus::Validated)
                ->whereMonth('payment_date', $monthToCalculate)
                ->whereYear('payment_date', $yearToCalculate)
                ->sum('amount_paid');

            // Add admin payments for current month
            $incomeCurrentMonth += Payment::where('is_paid', true)
                ->where('month', $monthToCalculate)
                ->where('year', $yearToCalculate)
                ->sum('amount');

            // Calculate accumulated income from January to current month
            $incomeAccumulated = PaymentReceipt::where('status', ReceiptStatus::Validated)
                ->whereYear('payment_date', $yearToCalculate)
                ->whereBetween('payment_date', [
                    now()->setYear($yearToCalculate)->startOfYear(),
                    now()->setYear($yearToCalculate)->setMonth($monthToCalculate)->endOfMonth(),
                ])
                ->sum('amount_paid');

            // Add admin payments accumulated
            $incomeAccumulated += Payment::where('is_paid', true)
                ->where('year', $yearToCalculate)
                ->whereBetween(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'), [
                    sprintf('%04d-01', $yearToCalculate),
                    sprintf('%04d-%02d', $yearToCalculate, $monthToCalculate),
                ])
                ->sum('amount');

            // Get monthly breakdown for details
            $incomeMonthlyDetails = PaymentReceipt::where('status', ReceiptStatus::Validated)
                ->whereYear('payment_date', $yearToCalculate)
                ->whereBetween('payment_date', [
                    now()->setYear($yearToCalculate)->startOfYear(),
                    now()->setYear($yearToCalculate)->setMonth($monthToCalculate)->endOfMonth(),
                ])
                ->selectRaw('MONTH(payment_date) as month, SUM(amount_paid) as total')
                ->groupByRaw('MONTH(payment_date)')
                ->get();

            // Add admin payments breakdown
            $adminMonthlyDetails = Payment::where('is_paid', true)
                ->where('year', $yearToCalculate)
                ->whereBetween(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'), [
                    sprintf('%04d-01', $yearToCalculate),
                    sprintf('%04d-%02d', $yearToCalculate, $monthToCalculate),
                ])
                ->selectRaw('month, SUM(amount) as total')
                ->groupByRaw('month')
                ->get();

            // Merge the two collections
            foreach ($adminMonthlyDetails as $adminDetail) {
                $existing = $incomeMonthlyDetails->firstWhere('month', $adminDetail->month);
                if ($existing) {
                    $existing->total += $adminDetail->total;
                } else {
                    $incomeMonthlyDetails->push($adminDetail);
                }
            }

            $incomeMonthlyDetails = $incomeMonthlyDetails->sortBy('month');

            // Build label
            $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $incomeLabel = 'Ingresos de '.$monthNames[(int) $monthToCalculate].' '.$yearToCalculate;
        } else {
            // School year view - calculate total for entire school year
            $incomeCurrentMonth = PaymentReceipt::where('status', ReceiptStatus::Validated)
                ->whereBetween('payment_date', [
                    $activeSchoolYear->start_date,
                    $activeSchoolYear->end_date,
                ])
                ->sum('amount_paid');

            // Add admin payments for school year
            $incomeCurrentMonth += Payment::where('is_paid', true)
                ->whereIn(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'),
                    $schoolYearMonths->map(function ($m) {
                        return sprintf('%04d-%02d', $m['year'], $m['month']);
                    })->toArray()
                )
                ->sum('amount');

            $incomeAccumulated = 0;
            $incomeLabel = 'Ingresos';
        }

        $rejectedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Rejected)->count();

        return view('finance.payment-receipts.index', compact(
            'receipts',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'incomeCurrentMonth',
            'incomeAccumulated',
            'incomeMonthlyDetails',
            'incomeLabel',
            'rejectedReceiptsCount',
            'monthLabel',
            'view'
        ));
    }

    public function create()
    {
        $students = Student::with('user')->get();

        return view('finance.payment-receipts.create', compact('students'));
    }

    public function getStudentParents(Student $student)
    {
        $parents = collect();

        if ($student->tutor_1_id) {
            $parents->push(User::find($student->tutor_1_id));
        }

        if ($student->tutor_2_id) {
            $parents->push(User::find($student->tutor_2_id));
        }

        return response()->json($parents->filter());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'parent_id' => 'required|exists:users,id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'reference' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'issuing_bank' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,transfer,card,check',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:pending,validated,rejected',
        ]);

        // Store the image
        $imagePath = $request->file('receipt_image')->store('payment-receipts', 'public');

        $validated['receipt_image'] = $imagePath;
        $validated['registered_by_id'] = auth()->id();

        if ($validated['status'] === 'validated') {
            $validated['validated_by'] = auth()->id();
            $validated['validated_at'] = now();
        }

        $receipt = PaymentReceipt::create($validated);

        // Log the status creation
        PaymentReceiptStatusLog::create([
            'payment_receipt_id' => $receipt->id,
            'changed_by_id' => auth()->id(),
            'previous_status' => null,
            'new_status' => $validated['status'],
            'notes' => 'Comprobante creado por '.auth()->user()->name,
        ]);

        return redirect()->route('finance.payment-receipts.index')
            ->with('success', 'Comprobante de pago registrado exitosamente.');
    }

    public function show(PaymentReceipt $paymentReceipt)
    {
        $paymentReceipt->load(['student.user', 'parent', 'registeredBy', 'validatedBy', 'statusLogs.changedBy']);

        return view('finance.payment-receipts.show', compact('paymentReceipt'));
    }

    public function updateStatus(Request $request, PaymentReceipt $paymentReceipt)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,validated,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
            'notes' => 'nullable|string',
        ]);

        $previousStatus = $paymentReceipt->status;

        $paymentReceipt->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'validated_by' => $validated['status'] === 'validated' ? auth()->id() : $paymentReceipt->validated_by,
            'validated_at' => $validated['status'] === 'validated' ? now() : $paymentReceipt->validated_at,
        ]);

        // Log the status change
        PaymentReceiptStatusLog::create([
            'payment_receipt_id' => $paymentReceipt->id,
            'changed_by_id' => auth()->id(),
            'previous_status' => $previousStatus,
            'new_status' => $validated['status'],
            'notes' => $validated['notes'] ?? 'Estado actualizado por '.auth()->user()->name,
        ]);

        return redirect()->route('finance.payment-receipts.show', $paymentReceipt)
            ->with('success', 'Estado del comprobante actualizado exitosamente.');
    }
}
