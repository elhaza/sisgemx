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

        $baseAmount = 3000.00;

        // Extract years from school year name (e.g., "2025-2026" -> 2025, 2026)
        $activeYears = explode('-', $activeSchoolYear->name);
        $activeStartYear = (int) $activeYears[0];
        $activeEndYear = (int) $activeYears[1];

        // School year runs from August to June (11 months)
        // Aug-Dec of start year, then Jan-June of end year
        $months = [
            ['year' => $activeStartYear, 'month' => 8],  // Agosto
            ['year' => $activeStartYear, 'month' => 9],  // Septiembre
            ['year' => $activeStartYear, 'month' => 10], // Octubre
            ['year' => $activeStartYear, 'month' => 11], // Noviembre
            ['year' => $activeStartYear, 'month' => 12], // Diciembre
            ['year' => $activeEndYear, 'month' => 1],    // Enero
            ['year' => $activeEndYear, 'month' => 2],    // Febrero
            ['year' => $activeEndYear, 'month' => 3],    // Marzo
            ['year' => $activeEndYear, 'month' => 4],    // Abril
            ['year' => $activeEndYear, 'month' => 5],    // Mayo
            ['year' => $activeEndYear, 'month' => 6],    // Junio
        ];

        // Active school year
        foreach ($months as $monthData) {
            MonthlyTuition::create([
                'school_year_id' => $activeSchoolYear->id,
                'year' => $monthData['year'],
                'month' => $monthData['month'],
                'amount' => $baseAmount,
            ]);
        }

        // Previous school year for payment history
        if ($previousSchoolYear) {
            $previousYears = explode('-', $previousSchoolYear->name);
            $previousStartYear = (int) $previousYears[0];
            $previousEndYear = (int) $previousYears[1];

            $previousMonths = [
                ['year' => $previousStartYear, 'month' => 8],
                ['year' => $previousStartYear, 'month' => 9],
                ['year' => $previousStartYear, 'month' => 10],
                ['year' => $previousStartYear, 'month' => 11],
                ['year' => $previousStartYear, 'month' => 12],
                ['year' => $previousEndYear, 'month' => 1],
                ['year' => $previousEndYear, 'month' => 2],
                ['year' => $previousEndYear, 'month' => 3],
                ['year' => $previousEndYear, 'month' => 4],
                ['year' => $previousEndYear, 'month' => 5],
                ['year' => $previousEndYear, 'month' => 6],
            ];

            foreach ($previousMonths as $monthData) {
                MonthlyTuition::create([
                    'school_year_id' => $previousSchoolYear->id,
                    'year' => $monthData['year'],
                    'month' => $monthData['month'],
                    'amount' => $baseAmount - 500, // Previous year was cheaper
                ]);
            }
        }

        $this->command->info('Colegiaturas mensuales creadas para ambos ciclos escolares');
    }
}
