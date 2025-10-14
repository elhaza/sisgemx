<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\ReceiptStatus;
use Illuminate\Support\Facades\DB;

class PaymentReceiptController extends Controller
{
    public function index()
    {
        $receipts = PaymentReceipt::query()
            ->with(['payment.student.user', 'parent'])
            ->where('status', ReceiptStatus::Pending)
            ->latest()
            ->paginate(15);

        $pendingReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Pending)->count();
        $validatedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Validated)->count();

        $monthlyIncome = PaymentReceipt::where('status', ReceiptStatus::Validated)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_paid');

        $activeDiscountsCount = Discount::whereHas('schoolYear', function ($query) {
            $query->where('is_active', true);
        })->count();

        return view('finance.payment-receipts.index', compact(
            'receipts',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'monthlyIncome',
            'activeDiscountsCount'
        ));
    }

    public function show(PaymentReceipt $paymentReceipt)
    {
        $paymentReceipt->load(['payment.student.user', 'parent']);

        return view('finance.payment-receipts.show', compact('paymentReceipt'));
    }
}
