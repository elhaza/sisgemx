<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleRun extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleRunFactory> */
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'created_by',
        'status',
        'notes',
    ];

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
