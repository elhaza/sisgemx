<?php

namespace App\Models;

use App\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'school_grade_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'classroom',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => DayOfWeek::class,
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolGrade(): BelongsTo
    {
        return $this->belongsTo(SchoolGrade::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Calculate duration in minutes from start and end times
     */
    public function getDurationMinutes(): int
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);

        return ($end - $start) / 60;
    }

    /**
     * Calculate duration in hours from start and end times
     */
    public function getDurationHours(): float
    {
        return round($this->getDurationMinutes() / 60, 1);
    }
}
