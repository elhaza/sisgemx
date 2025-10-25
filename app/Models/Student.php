<?php

namespace App\Models;

use App\Gender;
use App\StudentStatus;
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
        'status',
        'curp',
        'date_of_birth',
        'gender',
        'birth_country',
        'birth_state',
        'birth_city',
        'phone_number',
        'address',
        'parent_email',
        'tutor_1_id',
        'tutor_2_id',
        'requires_invoice',
        'billing_name',
        'billing_zip_code',
        'billing_rfc',
        'billing_tax_regime',
        'billing_cfdi_use',
        'tax_certificate_file',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'status' => StudentStatus::class,
            'date_of_birth' => 'date',
            'requires_invoice' => 'boolean',
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

    public function tutor1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_1_id');
    }

    public function tutor2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_2_id');
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

    public function tuitions(): HasMany
    {
        return $this->hasMany(StudentTuition::class);
    }

    public function pickupPeople(): HasMany
    {
        return $this->hasMany(PickupPerson::class);
    }

    /**
     * Check if a teacher teaches this student
     */
    public function hasTeacher($teacherId): bool
    {
        if (! $this->school_grade_id) {
            return false;
        }

        return Schedule::whereHas('subject', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
            ->where('school_grade_id', $this->school_grade_id)
            ->exists();
    }
}
