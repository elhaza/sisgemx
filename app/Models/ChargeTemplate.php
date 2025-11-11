<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargeTemplate extends Model
{
    protected $fillable = [
        'name',
        'charge_type',
        'description',
        'amount',
        'default_due_date',
        'school_year_id',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'default_due_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedCharges(): HasMany
    {
        return $this->hasMany(StudentAssignedCharge::class);
    }
}
