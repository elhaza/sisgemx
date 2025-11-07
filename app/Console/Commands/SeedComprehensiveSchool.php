<?php

namespace App\Console\Commands;

use Database\Seeders\ComprehensiveSchoolSeeder;
use Illuminate\Console\Command;

class SeedComprehensiveSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-comprehensive-school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta el seeder integral de la escuela 2025-2026 con 6 grupos, 84 alumnos, maestros y horarios completos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->confirm('⚠️  Este seeder conservará los datos existentes (announcements, settings, etc.). ¿Deseas continuar?', true)) {
            $seeder = new ComprehensiveSchoolSeeder;
            $seeder->setContainer($this->laravel);
            $seeder->setCommand($this);
            $seeder->run();

            return self::SUCCESS;
        }

        $this->info('Seeder cancelado.');

        return self::FAILURE;
    }
}
