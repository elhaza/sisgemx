<?php

namespace Database\Seeders;

use App\Models\ChargeTemplate;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class ChargeTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            $this->command->info('No active school year found. Skipping charge template seeder.');

            return;
        }

        // Crear cargos de ejemplo según el ciclo escolar
        $templates = [
            [
                'name' => 'Inscripción 2025',
                'charge_type' => 'inscription',
                'description' => 'Cargo de inscripción para el ciclo escolar 2025',
                'amount' => 1500.00,
                'default_due_date' => '2025-08-15',
            ],
            [
                'name' => 'Material Escolar 2025',
                'charge_type' => 'materials',
                'description' => 'Material de base para estudiantes',
                'amount' => 3500.00,
                'default_due_date' => '2025-08-15',
            ],
            [
                'name' => 'Material 5to Grado (Especial)',
                'charge_type' => 'materials',
                'description' => 'Material especializado para 5to grado',
                'amount' => 4000.00,
                'default_due_date' => '2025-08-15',
            ],
        ];

        foreach ($templates as $template) {
            ChargeTemplate::firstOrCreate(
                [
                    'name' => $template['name'],
                    'school_year_id' => $activeSchoolYear->id,
                ],
                [
                    'charge_type' => $template['charge_type'],
                    'description' => $template['description'],
                    'amount' => $template['amount'],
                    'default_due_date' => $template['default_due_date'],
                    'is_active' => true,
                    'created_by' => 1, // Asignar al primer usuario (admin)
                ]
            );
        }

        $this->command->info('Charge templates seeded successfully!');
    }
}
