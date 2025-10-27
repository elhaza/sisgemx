<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'title',
        'content',
        'image_path',
        'target_audience',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'target_audience' => 'array',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
