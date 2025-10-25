<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolGrade;
use App\Models\Subject;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageFilterController extends Controller
{
    /**
     * Obtener opciones de filtrado para un rol específico
     */
    public function getFilterOptions(Request $request): JsonResponse
    {
        $role = $request->query('role');
        $user = auth()->user();

        if (! $user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $options = match ($role) {
            'admin' => $this->getAdminOptions($user),
            'finance_admin' => $this->getFinanceAdminOptions($user),
            'teacher' => $this->getTeacherOptions($user),
            'parent' => $this->getParentOptions($user),
            'student' => $this->getStudentOptions($user),
            default => [],
        };

        return response()->json($options);
    }

    /**
     * Obtener opciones para administradores
     */
    private function getAdminOptions(User $user): array
    {
        return [
            'filters' => [
                ['type' => 'all', 'label' => 'Todos los administradores'],
                ['type' => 'individual', 'label' => 'Administrador individual'],
            ],
        ];
    }

    /**
     * Obtener opciones para finanzas
     */
    private function getFinanceAdminOptions(User $user): array
    {
        return [
            'filters' => [
                ['type' => 'all', 'label' => 'Todo el departamento de finanzas'],
                ['type' => 'individual', 'label' => 'Usuario de finanzas individual'],
            ],
        ];
    }

    /**
     * Obtener opciones para maestros
     */
    private function getTeacherOptions(User $user): array
    {
        return [
            'filters' => [
                ['type' => 'all', 'label' => 'Todos los maestros'],
                ['type' => 'by_level', 'label' => 'Por nivel'],
                ['type' => 'by_subject', 'label' => 'Por materia'],
                ['type' => 'by_school_grade', 'label' => 'Por grupo'],
                ['type' => 'individual', 'label' => 'Maestro individual'],
            ],
        ];
    }

    /**
     * Obtener opciones para padres
     */
    private function getParentOptions(User $user): array
    {
        return [
            'filters' => [
                ['type' => 'all', 'label' => 'Todos los padres'],
                ['type' => 'by_school_grade', 'label' => 'Por grado'],
                ['type' => 'by_school_grade_group', 'label' => 'Por grupo'],
                ['type' => 'by_student_name', 'label' => 'Por nombre del alumno'],
                ['type' => 'individual', 'label' => 'Padre individual'],
            ],
        ];
    }

    /**
     * Obtener opciones para estudiantes
     */
    private function getStudentOptions(User $user): array
    {
        return [
            'filters' => [
                ['type' => 'all', 'label' => 'Todos los estudiantes'],
                ['type' => 'by_school_grade', 'label' => 'Por grado'],
                ['type' => 'by_school_grade_group', 'label' => 'Por grupo'],
                ['type' => 'individual', 'label' => 'Estudiante individual'],
            ],
        ];
    }

    /**
     * Obtener datos para filtro específico
     */
    public function getFilterData(Request $request): JsonResponse
    {
        $role = $request->query('role');
        $filterType = $request->query('filter_type');
        $user = auth()->user();

        if (! $user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = match ($role) {
            'teacher' => $this->getTeacherFilterData($filterType),
            'parent' => $this->getParentFilterData($filterType),
            'student' => $this->getStudentFilterData($filterType),
            default => [],
        };

        return response()->json($data);
    }

    /**
     * Obtener datos de filtro para maestros
     */
    private function getTeacherFilterData(string $filterType): array
    {
        return match ($filterType) {
            'by_level' => [
                'items' => User::getAvailableLevels()->map(fn ($level) => [
                    'id' => $level,
                    'name' => "Nivel {$level}",
                ])->values()->all(),
            ],
            'by_subject' => [
                'items' => Subject::orderBy('name')->get()->unique('name')->map(fn ($subject) => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ])->values()->all(),
            ],
            'by_school_grade' => [
                'items' => SchoolGrade::orderBy('level')->orderBy('section')->get()->map(fn ($grade) => [
                    'id' => $grade->id,
                    'name' => "{$grade->name} - Sección {$grade->section}",
                ])->all(),
            ],
            default => [],
        };
    }

    /**
     * Obtener datos de filtro para padres
     */
    private function getParentFilterData(string $filterType): array
    {
        return match ($filterType) {
            'by_school_grade' => [
                'items' => SchoolGrade::orderBy('level')->orderBy('section')->get()->map(fn ($grade) => [
                    'id' => $grade->id,
                    'name' => $grade->name,
                ])->all(),
            ],
            'by_school_grade_group' => [
                'items' => SchoolGrade::orderBy('level')->orderBy('section')->get()->map(fn ($grade) => [
                    'id' => $grade->id,
                    'name' => "{$grade->name} - {$grade->section}",
                ])->all(),
            ],
            default => [],
        };
    }

    /**
     * Obtener datos de filtro para estudiantes
     */
    private function getStudentFilterData(string $filterType): array
    {
        return match ($filterType) {
            'by_school_grade' => [
                'items' => SchoolGrade::orderBy('level')->orderBy('section')->get()->map(fn ($grade) => [
                    'id' => $grade->id,
                    'name' => $grade->name,
                ])->all(),
            ],
            'by_school_grade_group' => [
                'items' => SchoolGrade::orderBy('level')->orderBy('section')->get()->map(fn ($grade) => [
                    'id' => $grade->id,
                    'name' => "{$grade->name} - {$grade->section}",
                ])->all(),
            ],
            default => [],
        };
    }

    /**
     * Obtener usuarios según rol y filtro
     */
    public function getUsers(Request $request): JsonResponse
    {
        $role = $request->query('role');
        $filterType = $request->query('filter_type');
        $filterId = $request->query('filter_id');
        $search = $request->query('search', '');
        $user = auth()->user();

        if (! $user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = $this->getUsersByFilter($role, $filterType, $filterId, $search, $user);

        return response()->json($users);
    }

    /**
     * Obtener usuarios filtrados
     */
    private function getUsersByFilter(string $role, string $filterType, ?string $filterId, string $search, User $authUser): array
    {
        $query = match ($role) {
            'admin' => $this->getAdminUsersQuery($filterType, $filterId, $search, $authUser),
            'finance_admin' => $this->getFinanceAdminUsersQuery($filterType, $filterId, $search, $authUser),
            'teacher' => $this->getTeacherUsersQuery($filterType, $filterId, $search, $authUser),
            'parent' => $this->getParentUsersQuery($filterType, $filterId, $search, $authUser),
            'student' => $this->getStudentUsersQuery($filterType, $filterId, $search, $authUser),
            default => [],
        };

        return is_array($query) ? $query : $this->formatUsersForResponse($query);
    }

    /**
     * Query para usuarios administrador
     */
    private function getAdminUsersQuery(string $filterType, ?string $filterId, string $search, User $authUser)
    {
        $query = User::where('role', UserRole::Admin)->where('id', '!=', $authUser->id);

        if ($filterType === 'individual' && $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Query para usuarios de finanzas
     */
    private function getFinanceAdminUsersQuery(string $filterType, ?string $filterId, string $search, User $authUser)
    {
        $query = User::where('role', UserRole::FinanceAdmin)->where('id', '!=', $authUser->id);

        if ($filterType === 'individual' && $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Query para maestros
     */
    private function getTeacherUsersQuery(string $filterType, ?string $filterId, string $search, User $authUser)
    {
        return match ($filterType) {
            'all' => User::getByRole(UserRole::Teacher, $authUser),
            'by_level' => User::getTeachersByLevel((int) $filterId),
            'by_subject' => User::getTeachersBySubject((int) $filterId),
            'by_school_grade' => User::getTeachersBySchoolGrade((int) $filterId),
            'individual' => User::where('role', UserRole::Teacher)
                ->where('id', '!=', $authUser->id)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('apellido_materno', 'like', "%{$search}%");
                }),
            default => collect(),
        };
    }

    /**
     * Query para padres
     */
    private function getParentUsersQuery(string $filterType, ?string $filterId, string $search, User $authUser)
    {
        return match ($filterType) {
            'all' => User::getByRole(UserRole::Parent, $authUser),
            'by_school_grade' => User::getParentsBySchoolGrade((int) $filterId),
            'by_school_grade_group' => User::getParentsBySchoolGradeGroup((int) $filterId),
            'by_student_name' => $this->getParentsByStudentName($search),
            'individual' => User::where('role', UserRole::Parent)
                ->where('id', '!=', $authUser->id)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('apellido_materno', 'like', "%{$search}%");
                }),
            default => collect(),
        };
    }

    /**
     * Query para estudiantes
     */
    private function getStudentUsersQuery(string $filterType, ?string $filterId, string $search, User $authUser)
    {
        return match ($filterType) {
            'all' => User::getByRole(UserRole::Student, $authUser),
            'by_school_grade' => User::getStudentsBySchoolGrade((int) $filterId),
            'by_school_grade_group' => User::getStudentsBySchoolGradeGroup((int) $filterId),
            'individual' => User::where('role', UserRole::Student)
                ->where('id', '!=', $authUser->id)
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('apellido_paterno', 'like', "%{$search}%")
                        ->orWhere('apellido_materno', 'like', "%{$search}%");
                }),
            default => collect(),
        };
    }

    /**
     * Obtener padres por nombre de estudiante
     */
    private function getParentsByStudentName(string $search)
    {
        return User::whereHas('children', function ($q) use ($search) {
            $q->where(function ($sq) use ($search) {
                $sq->where('name', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        })
            ->where('role', UserRole::Parent)
            ->distinct();
    }

    /**
     * Formatear usuarios para respuesta
     */
    private function formatUsersForResponse($users): array
    {
        if (is_a($users, 'Illuminate\Database\Eloquent\Builder')) {
            $users = $users->get();
        }

        return $users->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'role' => str_replace('_', ' ', $user->role->value),
        ])->all();
    }
}
