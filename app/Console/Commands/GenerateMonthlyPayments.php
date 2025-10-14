<?php

namespace App\Console\Commands;

use App\Models\Discount;
use App\Models\Payment;
use App\Models\PaymentsConfiguration;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyPayments extends Command
{
    protected $signature = 'payments:generate {config=default} {--year=} {--month=}';

    protected $description = 'Genera cargos de pago mensuales según la configuración de cobros y descuentos aplicables.';

    public function handle()
    {
        $configName = $this->argument('config') ?? 'default';
        $year = $this->option('year') ?? Carbon::now()->year;
        $month = $this->option('month') ?? Carbon::now()->month;

        $this->info("Generando pagos para configuration='{$configName}' year={$year} month={$month}...");

        $config = PaymentsConfiguration::where('name', $configName)->first();

        if (! $config) {
            $this->error('Payments configuration not found: '.$configName);

            return 1;
        }

        $months = $config->months_included ? json_decode($config->months_included, true) : [];
        if (! empty($months) && empty($months[$month])) {
            $this->info("El mes {$month} no está incluido en la configuración. Nada que generar.");

            return 0;
        }

        $fees_by_grade = $config->fees_by_grade ? json_decode($config->fees_by_grade, true) : [];

        $students = Student::where('enrollment_status', 'active')->get();

        foreach ($students as $student) {
            $grade_level = $student->grade_level;
            $fee = 0;
            if ($grade_level && isset($fees_by_grade[$grade_level])) {
                $fee = $fees_by_grade[$grade_level];
            } else {
                $settings = $config->settings ? json_decode($config->settings, true) : [];
                $fee = $settings['default_fee'] ?? 0;
            }

            $discountAmount = 0;
            $applicableDiscounts = Discount::where('active', true)
                ->where(function ($q) use ($student) {
                    $q->whereNull('applies_to')
                        ->orWhereRaw("JSON_EXTRACT(applies_to, '$$.students') IS NOT NULL AND JSON_CONTAINS(applies_to->'$.students', CAST(? AS JSON))", [json_encode($student->id)])
                        ->orWhereRaw("JSON_EXTRACT(applies_to, '$$.grade_levels') IS NOT NULL AND JSON_CONTAINS(applies_to->'$.grade_levels', CAST(? AS JSON))", [json_encode($student->grade_level)]);
                })->get();

            foreach ($applicableDiscounts as $d) {
                if ($d->kind === 'percentage') {
                    $discountAmount += ($fee * ($d->value / 100));
                } else {
                    $discountAmount += $d->value;
                }
            }

            $existing = Payment::where('student_id', $student->id)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existing) {
                $this->info("Pago ya existente para student {$student->id} {$year}-{$month}, saltando.");

                continue;
            }

            $payment = Payment::create([
                'payer_id' => $student->user_id,
                'student_id' => $student->id,
                'description' => "Mensualidad {$month}/{$year}",
                'amount' => $fee,
                'discount_amount' => round($discountAmount, 2),
                'paid_amount' => 0,
                'status' => $fee - $discountAmount <= 0 ? 'paid' : 'pending',
                'due_date' => Carbon::createFromDate($year, $month, 10)->toDateString(),
                'year' => $year,
                'month' => $month,
                'payments_configuration_id' => $config->id,
                'metadata' => json_encode(['generated_by' => 'payments:generate']),
            ]);

            $this->info("Generado pago id={$payment->id} student={$student->id} amount={$payment->amount} discount={$payment->discount_amount}");
        }

        $this->info('Generación completada.');

        return 0;
    }
}
