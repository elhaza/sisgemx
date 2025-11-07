<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        // Determine current school year based on date
        // If we're in Aug-Dec, use current year as start year, else use previous year
        $currentStartYear = ($now->month >= 8) ? $now->year : $now->year - 1;
        $currentEndYear = $currentStartYear + 1;
        $previousStartYear = $currentStartYear - 1;
        $previousEndYear = $currentStartYear;

        // Previous school year
        SchoolYear::create([
            'name' => $previousStartYear.'-'.$previousEndYear,
            'start_date' => $previousStartYear.'-08-15',
            'end_date' => $previousEndYear.'-06-30',
            'is_active' => false,
        ]);

        // Current school year
        SchoolYear::create([
            'name' => $currentStartYear.'-'.$currentEndYear,
            'start_date' => $currentStartYear.'-08-15',
            'end_date' => $currentEndYear.'-06-30',
            'is_active' => true,
        ]);

        $this->command->info('Ciclos escolares creados: '.$previousStartYear.'-'.$previousEndYear.' (anterior) y '.$currentStartYear.'-'.$currentEndYear.' (activo)');
    }
}
