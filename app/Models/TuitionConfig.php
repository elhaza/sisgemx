<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionConfig extends Model
{
    /** @use HasFactory<\Database\Factories\TuitionConfigFactory> */
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function studentTuitions()
    {
        return $this->hasMany(StudentTuition::class, 'school_year_id', 'school_year_id');
    }
}
