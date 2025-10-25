<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\PaymentReceiptStatusLog;
use App\Models\Student;
use App\ReceiptStatus;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function index()
    {
        $receipts = PaymentReceipt::query()
            ->where('parent_id', auth()->id())
            ->with(['student.user', 'registeredBy'])
            ->latest()
            ->paginate(15);

        $children = auth()->user()->student ? [auth()->user()->student] : [];

        $pendingReceiptsCount = PaymentReceipt::where('parent_id', auth()->id())
            ->where('status', ReceiptStatus::Pending)
            ->count();

        $validatedReceiptsCount = PaymentReceipt::where('parent_id', auth()->id())
            ->where('status', ReceiptStatus::Validated)
            ->count();

        $rejectedReceiptsCount = PaymentReceipt::where('parent_id', auth()->id())
            ->where('status', ReceiptStatus::Rejected)
            ->count();

        return view('parent.payment-receipts.index', compact(
            'receipts',
            'children',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'rejectedReceiptsCount'
        ));
    }

    public function create()
    {
        // Get students related to this parent
        $students = Student::whereHas('user.parent', function ($query) {
            $query->where('id', auth()->id());
        })->with('user')->get();

        // If parent doesn't have children, check if they themselves are a student
        if ($students->isEmpty() && auth()->user()->student) {
            $students = collect([auth()->user()->student]);
        }

        return view('parent.payment-receipts.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'reference' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'issuing_bank' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,transfer,card,check',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Store the image
        $imagePath = $request->file('receipt_image')->store('payment-receipts', 'public');

        $validated['receipt_image'] = $imagePath;
        $validated['parent_id'] = auth()->id();
        $validated['registered_by_id'] = auth()->id();
        $validated['status'] = ReceiptStatus::Pending;

        $receipt = PaymentReceipt::create($validated);

        // Log the status creation
        PaymentReceiptStatusLog::create([
            'payment_receipt_id' => $receipt->id,
            'changed_by_id' => auth()->id(),
            'previous_status' => null,
            'new_status' => ReceiptStatus::Pending,
            'notes' => 'Comprobante creado por el padre/tutor',
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
