<?php

namespace App\Models;

use App\Relationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupPerson extends Model
{
    /** @use HasFactory<\Database\Factories\PickupPersonFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'relationship',
        'face_photo',
        'id_photo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'relationship' => Relationship::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
