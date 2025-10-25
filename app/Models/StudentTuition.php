<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTuition extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'monthly_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
