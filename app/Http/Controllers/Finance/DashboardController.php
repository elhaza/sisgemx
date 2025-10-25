<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\StudentTuition;
use App\ReceiptStatus;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $unreadMessageCount = $user->unread_message_count;

        $pendingReceipts = PaymentReceipt::where('status', ReceiptStatus::Pending)->count();
        $validatedReceipts = PaymentReceipt::where('status', ReceiptStatus::Validated)->count();
        $rejectedReceipts = PaymentReceipt::where('status', ReceiptStatus::Rejected)->count();

        $totalPendingPayments = Payment::where('is_paid', false)->sum('amount');
        $totalPaidThisMonth = Payment::where('is_paid', true)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $studentsWithDiscounts = StudentTuition::where('discount_percentage', '>', 0)
            ->distinct('student_id')
            ->count();

        $recentReceipts = PaymentReceipt::with(['student.user', 'parent'])
            ->latest()
            ->take(10)
            ->get();

        return view('finance.dashboard', compact(
            'unreadMessageCount',
            'pendingReceipts',
            'validatedReceipts',
            'rejectedReceipts',
            'totalPendingPayments',
            'totalPaidThisMonth',
            'studentsWithDiscounts',
            'recentReceipts'
        ));
    }
}
