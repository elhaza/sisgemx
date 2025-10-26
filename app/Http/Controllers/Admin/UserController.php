<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('student')->latest();

        // Filter by name, email, or apellidos
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        }

        // Filter by role if provided
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Get per_page parameter from request (default to 15)
        $perPage = $request->get('per_page', 15);
        // Limit to allowed values to prevent abuse
        $allowedPerPage = [15, 30, 50, 100];
        if (! in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        $users = $query->paginate($perPage)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = UserRole::cases();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,finance_admin,teacher,parent,student',
        ];

        // For students, email is treated as a username and doesn't need email format
        if ($request->input('role') === 'student') {
            $rules['email'] = 'required|string|max:255|unique:users';
        } else {
            $rules['email'] = 'required|string|email|max:255|unique:users';
        }

        // Add teacher-specific validation rules
        if ($request->input('role') === 'teacher') {
            $rules['max_hours_per_day'] = 'nullable|numeric|min:1|max:12';
            $rules['max_hours_per_week'] = 'nullable|numeric|min:1|max:60';
        }

        $validated = $request->validate($rules);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        // Check if we should return to a specific URL with the created user
        if ($request->has('return_to') && $request->has('field')) {
            $returnUrl = $request->input('return_to');
            $field = $request->input('field');

            return redirect($returnUrl)->with([
                'success' => 'Usuario creado exitosamente.',
                'selected_user_id' => $user->id,
                'selected_field' => $field,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = UserRole::cases();
        $availabilities = $user->availabilities()->orderBy('day_of_week')->orderBy('start_time')->get();

        $dayNames = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return view('admin.users.edit', compact('user', 'roles', 'availabilities', 'dayNames'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'role' => 'required|in:admin,finance_admin,teacher,parent,student',
        ];

        // For students, email is treated as a username and doesn't need email format
        if ($request->input('role') === 'student') {
            $rules['email'] = 'required|string|max:255|unique:users,email,'.$user->id;
        } else {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,'.$user->id;
        }

        // Add teacher-specific validation rules
        if ($request->input('role') === 'teacher') {
            $rules['max_hours_per_day'] = 'nullable|numeric|min:1|max:12';
            $rules['max_hours_per_week'] = 'nullable|numeric|min:1|max:60';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = bcrypt($request->password);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function storeAvailability(Request $request, User $user): \Illuminate\Http\JsonResponse
    {
        // Ensure user is a teacher
        if ($user->role !== \App\UserRole::Teacher) {
            return response()->json(['message' => 'El usuario no es un maestro.'], 403);
        }

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'day_of_week.required' => 'El día de la semana es requerido.',
            'day_of_week.in' => 'El día de la semana no es válido.',
            'start_time.required' => 'La hora de inicio es requerida.',
            'start_time.date_format' => 'La hora de inicio debe estar en formato HH:mm.',
            'end_time.required' => 'La hora de fin es requerida.',
            'end_time.date_format' => 'La hora de fin debe estar en formato HH:mm.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // Check for duplicate availability (same day and time)
        $existingAvailability = $user->availabilities()
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', $validated['start_time'])
            ->where('end_time', $validated['end_time'])
            ->exists();

        if ($existingAvailability) {
            return response()->json(['message' => 'Ya existe una disponibilidad con estos datos.'], 422);
        }

        $user->availabilities()->create($validated);

        return response()->json(['message' => 'Disponibilidad agregada exitosamente.'], 201);
    }

    public function deleteAvailability(Request $request, User $user, int $availabilityId): \Illuminate\Http\JsonResponse
    {
        // Ensure user is a teacher
        if ($user->role !== \App\UserRole::Teacher) {
            return response()->json(['message' => 'El usuario no es un maestro.'], 403);
        }

        $availability = $user->availabilities()->findOrFail($availabilityId);
        $availability->delete();

        return response()->json(['message' => 'Disponibilidad eliminada exitosamente.'], 200);
    }
}
