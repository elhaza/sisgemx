<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrador',
            'apellido_paterno' => 'Sistema',
            'apellido_materno' => 'Escuela',
            'email' => 'admin@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        // Finance Admin
        User::create([
            'name' => 'Usuario',
            'apellido_paterno' => 'Finanzas',
            'apellido_materno' => 'Escuela',
            'email' => 'finanzas@escuela.com',
            'password' => Hash::make('password'),
            'role' => UserRole::FinanceAdmin,
        ]);

        // Teachers (10)
        $teacherNames = [
            ['María', 'García', 'López'],
            ['Juan', 'Martínez', 'Rodríguez'],
            ['Ana', 'Hernández', 'González'],
            ['Carlos', 'López', 'Pérez'],
            ['Laura', 'González', 'Sánchez'],
            ['Pedro', 'Rodríguez', 'Ramírez'],
            ['Carmen', 'Sánchez', 'Torres'],
            ['Miguel', 'Ramírez', 'Flores'],
            ['Isabel', 'Torres', 'Rivera'],
            ['Jorge', 'Flores', 'Gómez'],
        ];

        foreach ($teacherNames as $index => $name) {
            User::create([
                'name' => $name[0],
                'apellido_paterno' => $name[1],
                'apellido_materno' => $name[2],
                'email' => 'maestro'.($index + 1).'@escuela.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Teacher,
            ]);
        }

        // Parents (30)
        $parentNames = [
            ['Roberto', 'Gómez', 'Díaz'],
            ['Patricia', 'Díaz', 'Morales'],
            ['Fernando', 'Morales', 'Castro'],
            ['Claudia', 'Castro', 'Ortiz'],
            ['Ricardo', 'Ortiz', 'Ruiz'],
            ['Silvia', 'Ruiz', 'Mendoza'],
            ['Alberto', 'Mendoza', 'Vargas'],
            ['Monica', 'Vargas', 'Reyes'],
            ['Javier', 'Reyes', 'Cruz'],
            ['Andrea', 'Cruz', 'Jiménez'],
            ['Daniel', 'Jiménez', 'Navarro'],
            ['Gabriela', 'Navarro', 'Medina'],
            ['Sergio', 'Medina', 'Aguilar'],
            ['Verónica', 'Aguilar', 'Guerrero'],
            ['Arturo', 'Guerrero', 'Vega'],
            ['Mariana', 'Vega', 'Romero'],
            ['Raúl', 'Romero', 'Herrera'],
            ['Liliana', 'Herrera', 'Campos'],
            ['Héctor', 'Campos', 'Salazar'],
            ['Beatriz', 'Salazar', 'Cortés'],
            ['Ernesto', 'Cortés', 'Luna'],
            ['Carolina', 'Luna', 'Ríos'],
            ['Guillermo', 'Ríos', 'Chávez'],
            ['Alejandra', 'Chávez', 'Domínguez'],
            ['Rodrigo', 'Domínguez', 'Alvarado'],
            ['Paulina', 'Alvarado', 'Gutiérrez'],
            ['Óscar', 'Gutiérrez', 'Fuentes'],
            ['Diana', 'Fuentes', 'Valdez'],
            ['Enrique', 'Valdez', 'Estrada'],
            ['Lorena', 'Estrada', 'Molina'],
        ];

        foreach ($parentNames as $index => $name) {
            User::create([
                'name' => $name[0],
                'apellido_paterno' => $name[1],
                'apellido_materno' => $name[2],
                'email' => 'padre'.($index + 1).'@correo.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Parent,
            ]);
        }

        $this->command->info('Usuarios creados: 1 Admin, 1 Finanzas, 10 Maestros, 30 Padres');
    }
}
