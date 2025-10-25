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

        $query = PaymentReceipt::query()
            ->with(['student.user', 'parent', 'registeredBy']);

        if ($status) {
            $query->where('status', $status);
        }

        $receipts = $query->latest()->paginate(15);

        $pendingReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Pending)->count();
        $validatedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Validated)->count();

        $monthlyIncome = PaymentReceipt::where('status', ReceiptStatus::Validated)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_paid');

        $rejectedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Rejected)->count();

        return view('finance.payment-receipts.index', compact(
            'receipts',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'monthlyIncome',
            'rejectedReceiptsCount'
        ));
    }

    public function create()
    {
        $students = Student::with('user')->get();
        $parents = User::where('role', 'parent')->get();

        return view('finance.payment-receipts.create', compact('students', 'parents'));
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
