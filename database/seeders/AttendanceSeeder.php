<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::with('user')->get();
        $admin = User::where('role', UserRole::Admin)->first();

        // Last 30 school days (excluding weekends)
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(45);

        $attendanceRecords = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();

                continue;
            }

            foreach ($students as $student) {
                // 90% present, 5% late, 3% absent, 2% justified
                $random = rand(1, 100);

                if ($random <= 90) {
                    $status = 'present';
                    $arrivalTime = '08:00:00';
                    $minutesLate = null;
                } elseif ($random <= 95) {
                    $status = 'late';
                    $minutesLate = rand(5, 45);
                    $arrivalTime = Carbon::parse('08:00:00')->addMinutes($minutesLate)->format('H:i:s');
                } elseif ($random <= 98) {
                    $status = 'absent';
                    $arrivalTime = null;
                    $minutesLate = null;
                } else {
                    $status = 'justified';
                    $arrivalTime = null;
                    $minutesLate = null;
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'attendance_date' => $currentDate->format('Y-m-d'),
                    'status' => $status,
                    'arrival_time' => $arrivalTime,
                    'minutes_late' => $minutesLate,
                    'notes' => $status === 'late' && $minutesLate > 30 ? 'Retardo significativo' : null,
                    'recorded_by' => $admin->id,
                ]);

                $attendanceRecords++;
            }

            $currentDate->addDay();
        }

        $this->command->info("Asistencias creadas: {$attendanceRecords} registros (~30 días escolares)");
    }
}
