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

        return view('admin.users.edit', compact('user', 'roles'));
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
}
