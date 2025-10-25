<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolYearFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function tuitionConfigs(): HasMany
    {
        return $this->hasMany(TuitionConfig::class);
    }

    public function studentTuitions(): HasMany
    {
        return $this->hasMany(StudentTuition::class);
    }

    public function tuitionConfig()
    {
        return $this->hasOne(TuitionConfig::class);
    }

    public function monthlyTuitions(): HasMany
    {
        return $this->hasMany(MonthlyTuition::class);
    }

    public function schoolGrades(): HasMany
    {
        return $this->hasMany(SchoolGrade::class);
    }
}
