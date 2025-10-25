<?php

namespace Database\Seeders;

use App\Models\MonthlyTuition;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class MonthlyTuitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();
        $previousSchoolYear = SchoolYear::where('is_active', false)->first();

        $baseAmount = 1500.00;

        // School year runs from August to June (11 months)
        $months = [
            ['year' => 2024, 'month' => 8],  // Agosto
            ['year' => 2024, 'month' => 9],  // Septiembre
            ['year' => 2024, 'month' => 10], // Octubre
            ['year' => 2024, 'month' => 11], // Noviembre
            ['year' => 2024, 'month' => 12], // Diciembre
            ['year' => 2025, 'month' => 1],  // Enero
            ['year' => 2025, 'month' => 2],  // Febrero
            ['year' => 2025, 'month' => 3],  // Marzo
            ['year' => 2025, 'month' => 4],  // Abril
            ['year' => 2025, 'month' => 5],  // Mayo
            ['year' => 2025, 'month' => 6],  // Junio
        ];

        // Active school year (2024-2025)
        foreach ($months as $monthData) {
            MonthlyTuition::create([
                'school_year_id' => $activeSchoolYear->id,
                'year' => $monthData['year'],
                'month' => $monthData['month'],
                'amount' => $baseAmount,
            ]);
        }

        // Previous school year (2023-2024) for payment history
        $previousMonths = [
            ['year' => 2023, 'month' => 8],
            ['year' => 2023, 'month' => 9],
            ['year' => 2023, 'month' => 10],
            ['year' => 2023, 'month' => 11],
            ['year' => 2023, 'month' => 12],
            ['year' => 2024, 'month' => 1],
            ['year' => 2024, 'month' => 2],
            ['year' => 2024, 'month' => 3],
            ['year' => 2024, 'month' => 4],
            ['year' => 2024, 'month' => 5],
            ['year' => 2024, 'month' => 6],
        ];

        foreach ($previousMonths as $monthData) {
            MonthlyTuition::create([
                'school_year_id' => $previousSchoolYear->id,
                'year' => $monthData['year'],
                'month' => $monthData['month'],
                'amount' => $baseAmount - 100, // Previous year was cheaper
            ]);
        }

        $this->command->info('Colegiaturas mensuales creadas para ambos ciclos escolares');
    }
}
