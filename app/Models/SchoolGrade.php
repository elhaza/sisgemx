<?php

namespace App\Models;

use Database\Factories\SchoolGradeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolGrade extends Model
{
    /** @use HasFactory<SchoolGradeFactory> */
    use HasFactory;

    protected $fillable = [
        'level',
        'name',
        'section',
        'school_year_id',
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
