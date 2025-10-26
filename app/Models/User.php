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
        'max_hours_per_day',
        'max_hours_per_week',
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

    public function availabilities(): HasMany
    {
        return $this->hasMany(TeacherAvailability::class, 'teacher_id');
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

    // ==================== Métodos de Segmentación para Mensajes ====================

    /**
     * Obtener todos los usuarios de un rol específico
     */
    public static function getByRole(UserRole $role, ?User $excludeUser = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::where('role', $role);
        if ($excludeUser) {
            $query->where('id', '!=', $excludeUser->id);
        }

        return $query->get();
    }

    /**
     * Obtener maestros por nivel
     */
    public static function getTeachersByLevel(int $level): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('subjects', function ($q) use ($level) {
            $q->where('grade_level', $level);
        })
            ->where('role', UserRole::Teacher)
            ->distinct()
            ->get();
    }

    /**
     * Obtener maestros por materia
     */
    public static function getTeachersBySubject(int $subjectId): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('subjects', function ($q) use ($subjectId) {
            $q->where('id', $subjectId);
        })
            ->where('role', UserRole::Teacher)
            ->get();
    }

    /**
     * Obtener maestros por grupo/grado escolar
     */
    public static function getTeachersBySchoolGrade(int $schoolGradeId): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('subjects', function ($q) use ($schoolGradeId) {
            $q->whereHas('schedules', function ($sq) use ($schoolGradeId) {
                $sq->where('school_grade_id', $schoolGradeId);
            });
        })
            ->where('role', UserRole::Teacher)
            ->distinct()
            ->get();
    }

    /**
     * Obtener padres por grado escolar (sus hijos están en ese grado)
     */
    public static function getParentsBySchoolGrade(int $schoolGradeId): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('children', function ($q) use ($schoolGradeId) {
            $q->where('school_grade_id', $schoolGradeId);
        })
            ->where('role', UserRole::Parent)
            ->distinct()
            ->get();
    }

    /**
     * Obtener padres por grupo (sus hijos están en ese grupo)
     */
    public static function getParentsBySchoolGradeGroup(int $schoolGradeId): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('children', function ($q) use ($schoolGradeId) {
            $q->where('school_grade_id', $schoolGradeId);
        })
            ->where('role', UserRole::Parent)
            ->distinct()
            ->get();
    }

    /**
     * Obtener estudiantes por grado escolar
     */
    public static function getStudentsBySchoolGrade(int $schoolGradeId): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('student', function ($q) use ($schoolGradeId) {
            $q->where('school_grade_id', $schoolGradeId);
        })
            ->where('role', UserRole::Student)
            ->get();
    }

    /**
     * Obtener estudiantes por grupo (escuela/sección)
     */
    public static function getStudentsBySchoolGradeGroup(int $schoolGradeId): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereHas('student', function ($q) use ($schoolGradeId) {
            $q->where('school_grade_id', $schoolGradeId);
        })
            ->where('role', UserRole::Student)
            ->get();
    }

    /**
     * Obtener padre por estudiante
     */
    public static function getParentByStudent(int $studentId): ?self
    {
        return self::whereHas('children', function ($q) use ($studentId) {
            $q->where('id', $studentId);
        })
            ->where('role', UserRole::Parent)
            ->first();
    }

    /**
     * Obtener niveles disponibles (para filtrado)
     */
    public static function getAvailableLevels(): \Illuminate\Support\Collection
    {
        return GradeSection::distinct('grade_level')
            ->orderBy('grade_level')
            ->pluck('grade_level');
    }

    /**
     * Obtener grupos disponibles (para filtrado)
     */
    public static function getAvailableSchoolGrades(): \Illuminate\Database\Eloquent\Collection
    {
        return GradeSection::orderBy('grade_level')->orderBy('section')->get();
    }

    /**
     * Get teachers for a student with their subjects
     */
    public function getStudentTeachers(): \Illuminate\Support\Collection
    {
        if (! $this->isStudent() || ! $this->student) {
            return collect();
        }

        return Schedule::where('school_grade_id', $this->student->school_grade_id)
            ->with(['teacher', 'subject'])
            ->distinct('teacher_id')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->teacher->id,
                    'name' => $schedule->teacher->full_name,
                    'email' => $schedule->teacher->email,
                    'subject' => $schedule->subject?->name,
                ];
            })
            ->values();
    }
}
