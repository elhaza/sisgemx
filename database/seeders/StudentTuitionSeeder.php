<?php

namespace Database\Seeders;

use App\Helpers\PaymentHelper;
use App\Models\MonthlyTuition;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentTuition;
use Illuminate\Database\Seeder;

class StudentTuitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $students = Student::where('school_year_id', $activeSchoolYear->id)->get();
        $monthlyTuitions = MonthlyTuition::where('school_year_id', $activeSchoolYear->id)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        foreach ($students as $student) {
            foreach ($monthlyTuitions as $monthlyTuition) {
                // Give 10% of students a 5% discount, 5% a 10% discount
                $discountPercentage = 0;
                $random = rand(1, 100);
                if ($random <= 5) {
                    $discountPercentage = 10;
                } elseif ($random <= 15) {
                    $discountPercentage = 5;
                }

                $finalAmount = $monthlyTuition->amount * (1 - $discountPercentage / 100);

                // Calculate due date
                $dueDate = PaymentHelper::calculateDueDate($monthlyTuition->year, $monthlyTuition->month);

                StudentTuition::create([
                    'student_id' => $student->id,
                    'school_year_id' => $activeSchoolYear->id,
                    'monthly_tuition_id' => $monthlyTuition->id,
                    'year' => $monthlyTuition->year,
                    'month' => $monthlyTuition->month,
                    'monthly_amount' => $monthlyTuition->amount,
                    'discount_percentage' => $discountPercentage,
                    'final_amount' => $finalAmount,
                    'due_date' => $dueDate,
                ]);
            }
        }

        $this->command->info('Colegiaturas asignadas a todos los estudiantes del ciclo activo');
    }
}
