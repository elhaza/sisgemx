<?php

namespace App\Models;

use Database\Factories\SchoolGradeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolGrade extends Model
{
    /** @use HasFactory<SchoolGradeFactory> */
    use HasFactory;

    protected $table = 'grade_sections';

    protected $fillable = [
        'grade_level',
        'name',
        'section',
        'school_year_id',
        'break_time_start',
        'break_time_end',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function getFullNameAttribute()
    {
        return $this->name.' - Sección '.$this->section;
    }
}
