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
        // Previous school year (2023-2024)
        SchoolYear::create([
            'name' => '2023-2024',
            'start_date' => '2023-08-15',
            'end_date' => '2024-06-30',
            'is_active' => false,
        ]);

        // Current school year (2024-2025) - within current dates
        SchoolYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-08-15',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        $this->command->info('Ciclos escolares creados: 2023-2024 (anterior) y 2024-2025 (activo)');
    }
}
