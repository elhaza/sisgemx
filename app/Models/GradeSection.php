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
    ];

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

    public function getFullNameAttribute()
    {
        return $this->name.' - Sección '.$this->section;
    }
}
