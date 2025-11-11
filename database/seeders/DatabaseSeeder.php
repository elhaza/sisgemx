<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SchoolYearSeeder::class,
            GradeSectionSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            MonthlyTuitionSeeder::class,
            StudentTuitionSeeder::class,
            PaymentReceiptSeeder::class,
            ChargeTemplateSeeder::class,
        ]);

        // Get active school year for message
        $activeSchoolYear = \App\Models\SchoolYear::where('is_active', true)->first();
        $previousSchoolYear = \App\Models\SchoolYear::where('is_active', false)->first();

        $this->command->info('');
        $this->command->info('=== Base de datos poblada exitosamente! ===');
        $this->command->info('');
        $this->command->info('Usuarios de prueba (password: password):');
        $this->command->info('Admin: admin@escuela.com');
        $this->command->info('Finanzas: finanzas@escuela.com');
        $this->command->info('Padres: padre1@correo.com hasta padre30@correo.com');
        $this->command->info('Maestros: maestro1@escuela.com hasta maestro10@escuela.com');
        $this->command->info('');
        $this->command->info('Ciclos escolares: '.$activeSchoolYear->name.' (activo) y '.$previousSchoolYear->name.' (anterior)');
        $this->command->info('Grados: 1° a 6°, secciones A y B (168 estudiantes total)');
        $this->command->info('');
    }
}
