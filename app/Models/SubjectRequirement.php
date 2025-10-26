<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectRequirementFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'grade_section_id',
        'hours_per_day',
        'hours_per_week',
        'min_consecutive_minutes',
    ];

    protected function casts(): array
    {
        return [
            'hours_per_day' => 'decimal:2',
            'hours_per_week' => 'decimal:2',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function gradeSection(): BelongsTo
    {
        return $this->belongsTo(GradeSection::class);
    }
}
