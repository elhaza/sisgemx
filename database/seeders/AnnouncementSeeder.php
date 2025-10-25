<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = User::where('role', UserRole::Teacher)->get();
        $admin = User::where('role', UserRole::Admin)->first();

        $announcements = [
            [
                'title' => 'Bienvenida al Ciclo Escolar 2024-2025',
                'content' => 'Les damos la bienvenida a este nuevo ciclo escolar. Estamos emocionados de comenzar este año lleno de aprendizaje y crecimiento.',
                'author' => $admin,
                'target_audience' => 'all',
                'days_ago' => 60,
            ],
            [
                'title' => 'Junta de Padres - Próximo Viernes',
                'content' => 'Se llevará a cabo una junta de padres de familia el próximo viernes a las 18:00 horas. Es importante su asistencia para tratar temas importantes del aprovechamiento escolar.',
                'author' => $teachers->random(),
                'target_audience' => 'parents',
                'days_ago' => 5,
            ],
            [
                'title' => 'Horario de Receso',
                'content' => 'Recordatorio: El horario de receso es de 10:00 a 10:30 hrs. Por favor asegúrense de traer su lunch saludable.',
                'author' => $teachers->random(),
                'target_audience' => 'students',
                'days_ago' => 15,
            ],
            [
                'title' => 'Suspensión de Clases - Día Festivo',
                'content' => 'Les informamos que habrá suspensión de clases el próximo lunes por motivo del día festivo nacional.',
                'author' => $admin,
                'target_audience' => 'all',
                'days_ago' => 10,
            ],
            [
                'title' => 'Festival de Primavera',
                'content' => 'Estamos preparando nuestro festival de primavera. Todos los estudiantes participarán con presentaciones artísticas. Más información próximamente.',
                'author' => $teachers->random(),
                'target_audience' => 'all',
                'days_ago' => 20,
            ],
            [
                'title' => 'Entrega de Boletas',
                'content' => 'La entrega de boletas del primer bimestre se realizará la próxima semana. Se les notificará el día y hora específicos por grupo.',
                'author' => $admin,
                'target_audience' => 'parents',
                'days_ago' => 3,
            ],
            [
                'title' => 'Tarea de Ciencias Naturales',
                'content' => 'Recuerden entregar su proyecto de ciencias naturales el próximo miércoles. Deben incluir su investigación sobre el sistema solar.',
                'author' => $teachers->random(),
                'target_audience' => 'students',
                'days_ago' => 7,
            ],
            [
                'title' => 'Protocolo de Entrada y Salida',
                'content' => 'Por seguridad, recordamos que el horario de entrada es de 7:45 a 8:00 hrs y la salida a las 13:30 hrs. Solo las personas autorizadas pueden recoger a los estudiantes.',
                'author' => $admin,
                'target_audience' => 'parents',
                'days_ago' => 25,
            ],
        ];

        foreach ($announcements as $announcementData) {
            Announcement::create([
                'title' => $announcementData['title'],
                'content' => $announcementData['content'],
                'teacher_id' => $announcementData['author']->id,
                'target_audience' => $announcementData['target_audience'],
                'created_at' => Carbon::now()->subDays($announcementData['days_ago']),
                'updated_at' => Carbon::now()->subDays($announcementData['days_ago']),
            ]);
        }

        $this->command->info('Avisos creados: '.count($announcements).' anuncios para diferentes audiencias');
    }
}
