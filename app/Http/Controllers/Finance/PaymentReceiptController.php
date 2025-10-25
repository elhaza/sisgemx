<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\PaymentReceiptStatusLog;
use App\Models\Student;
use App\Models\User;
use App\ReceiptStatus;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $month = $request->get('month');
        $year = $request->get('year');

        $query = PaymentReceipt::query()
            ->with(['student.user', 'parent', 'registeredBy']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($month) {
            $query->whereMonth('payment_date', $month);
        }

        if ($year) {
            $query->whereYear('payment_date', $year);
        }

        $receipts = $query->latest()->paginate(15)->withQueryString();

        $pendingReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Pending)->count();
        $validatedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Validated)->count();

        // Calculate income based on filters or default to current month
        $incomeQuery = PaymentReceipt::where('status', ReceiptStatus::Validated);

        if ($month) {
            $incomeQuery->whereMonth('payment_date', $month);
        } elseif (! $year) {
            // Default to current month if no month/year filter
            $incomeQuery->whereMonth('payment_date', now()->month);
        }

        if ($year) {
            $incomeQuery->whereYear('payment_date', $year);
        } elseif (! $month) {
            // Default to current year if no year/month filter
            $incomeQuery->whereYear('payment_date', now()->year);
        }

        $income = $incomeQuery->sum('amount_paid');

        // Build dynamic income label
        $incomeLabel = 'Ingresos';
        if ($month && $year) {
            $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $incomeLabel = 'Ingresos de '.$monthNames[(int) $month].' '.$year;
        } elseif ($month) {
            $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $incomeLabel = 'Ingresos de '.$monthNames[(int) $month];
        } elseif ($year) {
            $incomeLabel = 'Ingresos de '.$year;
        } else {
            $incomeLabel = 'Ingresos del Mes';
        }

        $rejectedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Rejected)->count();

        return view('finance.payment-receipts.index', compact(
            'receipts',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'income',
            'incomeLabel',
            'rejectedReceiptsCount'
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
