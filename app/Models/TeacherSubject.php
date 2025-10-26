<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubject extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherSubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'proficiency',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
