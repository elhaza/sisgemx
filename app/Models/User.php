<?php

namespace App\Models;

use App\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'role',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'teacher_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isFinanceAdmin(): bool
    {
        return $this->role === UserRole::FinanceAdmin;
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRole::Teacher;
    }

    public function isParent(): bool
    {
        return $this->role === UserRole::Parent;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
    }

    public function messageRecipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class, 'recipient_id');
    }

    public function getUnreadMessageCountAttribute(): int
    {
        return $this->messageRecipients()
            ->whereNull('read_at')
            ->count();
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->apellido_paterno,
            $this->apellido_materno,
            $this->name,
        ]);

        return implode(' ', $parts);
    }
}
