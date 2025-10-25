<?php

namespace App\Models;

use App\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_year_id',
        'school_grade_id',
        'enrollment_number',
        'grade_level',
        'group',
        'curp',
        'gender',
        'birth_country',
        'birth_state',
        'birth_city',
        'phone_number',
        'address',
        'parent_email',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function schoolGrade(): BelongsTo
    {
        return $this->belongsTo(SchoolGrade::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function medicalJustifications(): HasMany
    {
        return $this->hasMany(MedicalJustification::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    public function tuitions(): HasMany
    {
        return $this->hasMany(StudentTuition::class);
    }
}
