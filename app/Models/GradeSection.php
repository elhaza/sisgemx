<?php

namespace App\Models;

use Database\Factories\GradeSectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeSection extends Model
{
    /** @use HasFactory<GradeSectionFactory> */
    use HasFactory;

    protected $table = 'grade_sections';

    protected $fillable = [
        'grade_level',
        'section',
        'school_year_id',
        'break_time_start',
        'break_time_end',
        'name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->name && $model->grade_level && $model->section) {
                $model->name = $model->grade_level.$model->section;
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty(['grade_level', 'section']) && ! $model->isDirty('name')) {
                $model->name = $model->grade_level.$model->section;
            }
        });
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'school_grade_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'school_grade_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->grade_level.' - Sección '.$this->section;
    }

    public function getBreakTime(): array
    {
        // If group has its own break time, use it
        if ($this->break_time_start && $this->break_time_end) {
            return [
                'start' => $this->break_time_start,
                'end' => $this->break_time_end,
            ];
        }

        // Otherwise, use the global default from Settings
        return Settings::getDefaultBreakTime();
    }
}
