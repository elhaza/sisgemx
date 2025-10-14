<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalJustification extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalJustificationFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'parent_id',
        'absence_date',
        'reason',
        'document_file_path',
    ];

    protected function casts(): array
    {
        return [
            'absence_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
