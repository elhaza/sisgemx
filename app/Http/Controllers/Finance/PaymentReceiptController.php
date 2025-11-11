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
use Maatwebsite\Excel\Facades\Excel;

class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $month = $request->has('month') ? (int) $request->get('month') : null;
        $year = $request->has('year') ? (int) $request->get('year') : null;
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
            ->with(['student.user', 'parent', 'registeredBy', 'validatedBy', 'statusLogs.changedBy'])
            ->whereNotNull('student_id');

        if ($status) {
            $query->where('status', $status);
        }

        // Handle month view
        if ($view === 'month') {
            $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

            // If month is provided, filter by specific month; otherwise show all pending
            if ($month) {
                $monthToDisplay = (int) $month;
                $yearToDisplay = $year ?? now()->year;
                $query->where('payment_month', $monthToDisplay)
                    ->where('payment_year', $yearToDisplay);
                $monthLabel = $monthNames[$monthToDisplay].' '.$yearToDisplay;
            } else {
                // No specific month provided - show all for the year (or all if filtering by status like pending)
                $yearToDisplay = $year ?? now()->year;
                $query->where('payment_year', $yearToDisplay);
                $monthLabel = 'Todos los meses de '.$yearToDisplay;
            }
        } elseif ($activeSchoolYear && $view === 'school_year') {
            // Filter by school year months
            $query->where(function ($q) use ($activeSchoolYear) {
                // Include receipts with payment_month/year in school year range
                $q->whereYear('payment_date', '>=', $activeSchoolYear->start_date->year)
                    ->whereYear('payment_date', '<=', $activeSchoolYear->end_date->year);
            });
        }

        $receipts = $query->latest()->paginate(15)->withQueryString();

        // Get Payments registered by admin
        $paymentQuery = Payment::query()
            ->with(['student.user', 'studentTuition'])
            ->where('is_paid', true);

        if ($view === 'month') {
            $yearToDisplay = $year ?: now()->year;
            if ($month) {
                // Specific month selected
                $monthToDisplay = (int) $month;
                $paymentQuery->where('month', $monthToDisplay)
                    ->where('year', $yearToDisplay);
            } else {
                // No specific month - show all for the year
                $paymentQuery->where('year', $yearToDisplay);
            }
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
        $yearToCalculate = $year ?? now()->year;

        $incomeCurrentMonth = 0;
        $incomeAccumulated = 0;
        $incomeMonthlyDetails = collect();
        $advancePaymentsDetails = collect();
        $incomeLabel = 'Ingresos';

        if ($view === 'month') {
            if ($month) {
                // Specific month selected - show income for that month
                $monthToCalculate = (int) $month;

                // Calculate income for the selected month
                $incomeCurrentMonth = PaymentReceipt::where('status', ReceiptStatus::Validated)
                    ->where('payment_month', $monthToCalculate)
                    ->where('payment_year', $yearToCalculate)
                    ->sum('amount_paid');

                // Add admin payments for current month
                $incomeCurrentMonth += Payment::where('is_paid', true)
                    ->where('month', $monthToCalculate)
                    ->where('year', $yearToCalculate)
                    ->sum('amount');

                // Calculate accumulated income from January to current month
                $incomeAccumulated = PaymentReceipt::where('status', ReceiptStatus::Validated)
                    ->where('payment_year', $yearToCalculate)
                    ->whereBetween('payment_month', [1, $monthToCalculate])
                    ->sum('amount_paid');

                // Add admin payments accumulated (including advance payments made in current month)
                // Use paid_at to include payments made in the current month, regardless of what month they're for
                $incomeAccumulated += Payment::where('is_paid', true)
                    ->whereBetween('paid_at', [
                        now()->setYear($yearToCalculate)->setMonth(1)->startOfMonth(),
                        now()->setYear($yearToCalculate)->setMonth($monthToCalculate)->endOfMonth(),
                    ])
                    ->sum('amount');

                // Get monthly breakdown for details
                $incomeMonthlyDetails = PaymentReceipt::where('status', ReceiptStatus::Validated)
                    ->where('payment_year', $yearToCalculate)
                    ->whereBetween('payment_month', [1, $monthToCalculate])
                    ->selectRaw('payment_month as month, SUM(amount_paid) as total')
                    ->groupByRaw('payment_month')
                    ->get();
            } else {
                // No specific month - show total for all months in year
                $incomeCurrentMonth = PaymentReceipt::where('status', ReceiptStatus::Validated)
                    ->where('payment_year', $yearToCalculate)
                    ->sum('amount_paid');

                // Add admin payments for entire year
                $incomeCurrentMonth += Payment::where('is_paid', true)
                    ->where('year', $yearToCalculate)
                    ->sum('amount');

                // Get monthly breakdown for all months
                $incomeMonthlyDetails = PaymentReceipt::where('status', ReceiptStatus::Validated)
                    ->where('payment_year', $yearToCalculate)
                    ->selectRaw('payment_month as month, SUM(amount_paid) as total')
                    ->groupByRaw('payment_month')
                    ->get();
            }

            // Only show admin details and advance payments if a specific month is selected
            if ($month) {
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

                // Get advance payments made in current month for future months
                $advancePayments = Payment::where('is_paid', true)
                    ->whereBetween('paid_at', [
                        now()->setYear($yearToCalculate)->setMonth($monthToCalculate)->startOfMonth(),
                        now()->setYear($yearToCalculate)->setMonth($monthToCalculate)->endOfMonth(),
                    ])
                    ->where(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'), '>', sprintf('%04d-%02d', $yearToCalculate, $monthToCalculate))
                    ->selectRaw('month, year, SUM(amount) as total')
                    ->groupByRaw('month, year')
                    ->get();

                // Create a label for advance payments
                $advancePaymentsDetails = collect();
                if ($advancePayments->count() > 0) {
                    $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    foreach ($advancePayments as $advance) {
                        $advancePaymentsDetails->push((object) [
                            'label' => 'Adelanto para '.$monthNames[(int) $advance->month].' '.$advance->year,
                            'total' => $advance->total,
                        ]);
                    }
                }

                // Build label
                $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $incomeLabel = 'Ingresos de '.$monthNames[(int) $monthToCalculate].' '.$yearToCalculate;
            } else {
                // When no specific month, show all month breakdown without admin details (already included above)
                $incomeMonthlyDetails = $incomeMonthlyDetails->sortBy('month');
                $monthNames = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $incomeLabel = 'Ingresos de '.$yearToCalculate;
            }
        } else {
            // School year view - calculate total for entire school year
            $incomeCurrentMonth = PaymentReceipt::where('status', ReceiptStatus::Validated)
                ->whereIn(DB::raw('CONCAT(payment_year, "-", LPAD(payment_month, 2, "0"))'),
                    $schoolYearMonths->map(function ($m) {
                        return sprintf('%04d-%02d', $m['year'], $m['month']);
                    })->toArray()
                )
                ->sum('amount_paid');

            // Add admin payments for school year
            $incomeCurrentMonth += Payment::where('is_paid', true)
                ->whereIn(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'),
                    $schoolYearMonths->map(function ($m) {
                        return sprintf('%04d-%02d', $m['year'], $m['month']);
                    })->toArray()
                )
                ->sum('amount');

            // Get monthly breakdown for school year
            $incomeMonthlyDetails = PaymentReceipt::where('status', ReceiptStatus::Validated)
                ->whereIn(DB::raw('CONCAT(payment_year, "-", LPAD(payment_month, 2, "0"))'),
                    $schoolYearMonths->map(function ($m) {
                        return sprintf('%04d-%02d', $m['year'], $m['month']);
                    })->toArray()
                )
                ->selectRaw('payment_year as year, payment_month as month, SUM(amount_paid) as total')
                ->groupByRaw('payment_year, payment_month')
                ->get();

            // Add admin payments breakdown for school year
            $adminMonthlyDetails = Payment::where('is_paid', true)
                ->whereIn(DB::raw('CONCAT(year, "-", LPAD(month, 2, "0"))'),
                    $schoolYearMonths->map(function ($m) {
                        return sprintf('%04d-%02d', $m['year'], $m['month']);
                    })->toArray()
                )
                ->selectRaw('year, month, SUM(amount) as total')
                ->groupByRaw('year, month')
                ->get();

            // Merge the two collections
            foreach ($adminMonthlyDetails as $adminDetail) {
                $existing = $incomeMonthlyDetails->first(function ($item) use ($adminDetail) {
                    return $item->month == $adminDetail->month && $item->year == $adminDetail->year;
                });
                if ($existing) {
                    $existing->total += $adminDetail->total;
                } else {
                    $incomeMonthlyDetails->push((object) [
                        'year' => $adminDetail->year,
                        'month' => $adminDetail->month,
                        'total' => $adminDetail->total,
                    ]);
                }
            }

            // Sort by year then month
            $incomeMonthlyDetails = $incomeMonthlyDetails->sortBy(function ($item) {
                return $item->year * 100 + $item->month;
            });

            $incomeAccumulated = 0;
            $incomeLabel = 'Ingresos del ciclo escolar';
        }

        $rejectedReceiptsCount = PaymentReceipt::where('status', ReceiptStatus::Rejected)->count();

        // Get all validated receipts (both validated and admin payments) for the modal
        $validatedReceiptsList = PaymentReceipt::where('status', ReceiptStatus::Validated)
            ->with(['student.user', 'parent', 'registeredBy']);

        if ($view === 'month') {
            $yearToDisplay = $year ?? now()->year;
            if ($month) {
                // Specific month selected
                $monthToDisplay = (int) $month;
                $validatedReceiptsList = $validatedReceiptsList
                    ->where('payment_month', $monthToDisplay)
                    ->where('payment_year', $yearToDisplay);
            } else {
                // No specific month - show all for the year
                $validatedReceiptsList = $validatedReceiptsList
                    ->where('payment_year', $yearToDisplay);
            }
        } elseif ($activeSchoolYear && $view === 'school_year') {
            $validatedReceiptsList = $validatedReceiptsList->whereIn(DB::raw('CONCAT(payment_year, "-", LPAD(payment_month, 2, "0"))'),
                $schoolYearMonths->map(function ($m) {
                    return sprintf('%04d-%02d', $m['year'], $m['month']);
                })->toArray()
            );
        }

        $validatedReceiptsList = $validatedReceiptsList->get()->map(function ($receipt) {
            $receipt->receipt_image_url = $receipt->receipt_image ? Storage::url($receipt->receipt_image) : null;
            $receipt->type = 'validated_receipt';

            return $receipt;
        });

        // Combine both collections
        $allValidatedReceipts = $validatedReceiptsList->concat($adminPaymentReceipts);

        // Sort by date descending
        $allValidatedReceipts = $allValidatedReceipts->sortByDesc(function ($receipt) {
            // Handle both array and object access
            if (is_array($receipt)) {
                $date = $receipt['payment_date'] ?? $receipt['created_at'] ?? now();
            } else {
                $date = $receipt->payment_date ?? $receipt->created_at ?? now();
            }

            if (is_string($date)) {
                $date = \Carbon\Carbon::parse($date);
            }

            return $date instanceof \Carbon\Carbon ? $date->getTimestamp() : now()->getTimestamp();
        })->values();

        return view('finance.payment-receipts.index', compact(
            'receipts',
            'pendingReceiptsCount',
            'validatedReceiptsCount',
            'incomeCurrentMonth',
            'incomeAccumulated',
            'incomeMonthlyDetails',
            'advancePaymentsDetails',
            'incomeLabel',
            'rejectedReceiptsCount',
            'monthLabel',
            'view',
            'allValidatedReceipts'
        ));
    }

    public function create()
    {
        $students = Student::with('user')
            ->where('status', 'active')
            ->orderBy('user_id')
            ->get();

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

        // Extract payment_month and payment_year from payment_date
        $paymentDate = \Carbon\Carbon::parse($validated['payment_date']);
        $validated['payment_month'] = $paymentDate->month;
        $validated['payment_year'] = $paymentDate->year;

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

    public function bulkUpdateStatus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,validated,rejected',
            'receipt_ids' => 'required|string',
        ]);

        $receiptIds = array_filter(explode(',', $validated['receipt_ids']));

        if (empty($receiptIds)) {
            return back()->with('error', 'No se seleccionaron comprobantes.');
        }

        $receipts = PaymentReceipt::whereIn('id', $receiptIds)->get();

        foreach ($receipts as $receipt) {
            $previousStatus = $receipt->status;

            $receipt->update([
                'status' => $validated['status'],
                'validated_by' => $validated['status'] === 'validated' ? auth()->id() : $receipt->validated_by,
                'validated_at' => $validated['status'] === 'validated' ? now() : $receipt->validated_at,
                'rejection_reason' => $validated['status'] === 'rejected' ? 'Rechazado en acción masiva' : null,
            ]);

            // Log the status change
            PaymentReceiptStatusLog::create([
                'payment_receipt_id' => $receipt->id,
                'changed_by_id' => auth()->id(),
                'previous_status' => $previousStatus,
                'new_status' => $validated['status'],
                'notes' => 'Actualización masiva por '.auth()->user()->name,
            ]);
        }

        $message = match ($validated['status']) {
            'validated' => 'Se validaron '.count($receipts).' comprobante(s) exitosamente.',
            'rejected' => 'Se rechazaron '.count($receipts).' comprobante(s) exitosamente.',
            'pending' => 'Se marcaron '.count($receipts).' comprobante(s) como pendientes.',
        };

        return back()->with('success', $message);
    }

    public function exportExcel(Request $request)
    {
        $status = $request->query('status');
        $month = $request->query('month');
        $year = $request->query('year');
        $view = $request->query('view', 'month');

        // Build query
        $query = PaymentReceipt::query()->with(['student.user', 'parent', 'registeredBy']);

        if ($status) {
            $query->where('status', $status);
        }

        // Handle month view
        if ($view === 'month') {
            $yearToDisplay = $year ?: now()->year;
            if ($month) {
                $monthToDisplay = (int) $month;
                $query->where('payment_month', $monthToDisplay)
                    ->where('payment_year', $yearToDisplay);
            } else {
                $query->where('payment_year', $yearToDisplay);
            }
        }

        $receipts = $query->latest()->get();

        $fileName = 'comprobantes-pago-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(
            new \App\Exports\PaymentReceiptsExport($receipts->toArray()),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $status = $request->query('status');
        $month = $request->query('month');
        $year = $request->query('year');
        $view = $request->query('view', 'month');

        // Build query
        $query = PaymentReceipt::query()->with(['student.user', 'parent', 'registeredBy']);

        if ($status) {
            $query->where('status', $status);
        }

        // Handle month view
        if ($view === 'month') {
            $yearToDisplay = $year ?: now()->year;
            if ($month) {
                $monthToDisplay = (int) $month;
                $query->where('payment_month', $monthToDisplay)
                    ->where('payment_year', $yearToDisplay);
            } else {
                $query->where('payment_year', $yearToDisplay);
            }
        }

        $receipts = $query->latest()->get();

        $html = view('finance.payment-receipts.pdf', [
            'receipts' => $receipts,
            'status' => $status,
            'month' => $month,
            'year' => $year,
        ])->render();

        // Use wkhtmltopdf via shell_exec if available, otherwise return HTML
        $fileName = 'comprobantes-pago-'.now()->format('Y-m-d-His').'.pdf';

        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
