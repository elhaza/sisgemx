<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAvailability extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherAvailabilityFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'note',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
