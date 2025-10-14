<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\MedicalJustification;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\ReceiptStatus;

class PaymentReceiptController extends Controller
{
    public function index()
    {
        $receipts = PaymentReceipt::query()
            ->where('parent_id', auth()->id())
            ->with(['payment.student'])
            ->latest()
            ->paginate(15);

        $children = Student::whereHas('user', function ($query) {
            $query->where('parent_id', auth()->id());
        })->with('user')->get();

        $pendingPaymentsCount = Payment::whereHas('student.user', function ($query) {
            $query->where('parent_id', auth()->id());
        })->where('status', 'pending')->count();

        $validatedReceiptsCount = PaymentReceipt::where('parent_id', auth()->id())
            ->where('status', ReceiptStatus::Validated)
            ->count();

        $medicalJustificationsCount = MedicalJustification::whereHas('student.user', function ($query) {
            $query->where('parent_id', auth()->id());
        })->count();

        return view('parent.payment-receipts.index', compact(
            'receipts',
            'children',
            'pendingPaymentsCount',
            'validatedReceiptsCount',
            'medicalJustificationsCount'
        ));
    }
}
