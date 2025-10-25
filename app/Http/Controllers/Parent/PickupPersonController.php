<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\PickupPerson;
use App\Models\Student;
use App\Relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PickupPersonController extends Controller
{
    public function index(Student $student)
    {
        $this->authorize('view', $student);

        $pickupPeople = $student->pickupPeople()->get();
        $limit = config('app.people_pickup_limit', 3);
        $canAddMore = $pickupPeople->count() < $limit;

        return view('parent.pickup-people.index', compact('student', 'pickupPeople', 'canAddMore', 'limit'));
    }

    public function create(Student $student)
    {
        $this->authorize('view', $student);

        $limit = config('app.people_pickup_limit', 3);
        if ($student->pickupPeople()->count() >= $limit) {
            return redirect()->route('parent.pickup-people.index', $student)
                ->with('error', "Ya se ha alcanzado el límite de {$limit} personas autorizadas para recoger al estudiante.");
        }

        $relationships = Relationship::cases();

        return view('parent.pickup-people.create', compact('student', 'relationships'));
    }

    public function store(Request $request, Student $student)
    {
        $this->authorize('view', $student);

        $limit = config('app.people_pickup_limit', 3);
        if ($student->pickupPeople()->count() >= $limit) {
            return redirect()->route('parent.pickup-people.index', $student)
                ->with('error', "Ya se ha alcanzado el límite de {$limit} personas autorizadas para recoger al estudiante.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string',
            'face_photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'id_photo' => 'nullable|image|mimes:jpeg,jpg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('face_photo')) {
            $validated['face_photo'] = $request->file('face_photo')->store('pickup-people/faces', 'public');
        }

        if ($request->hasFile('id_photo')) {
            $validated['id_photo'] = $request->file('id_photo')->store('pickup-people/ids', 'public');
        }

        $student->pickupPeople()->create($validated);

        return redirect()->route('parent.pickup-people.index', $student)
            ->with('success', 'Persona autorizada agregada exitosamente.');
    }

    public function edit(Student $student, PickupPerson $pickupPerson)
    {
        $this->authorize('view', $student);

        if ($pickupPerson->student_id !== $student->id) {
            abort(404);
        }

        $relationships = Relationship::cases();

        return view('parent.pickup-people.edit', compact('student', 'pickupPerson', 'relationships'));
    }

    public function update(Request $request, Student $student, PickupPerson $pickupPerson)
    {
        $this->authorize('view', $student);

        if ($pickupPerson->student_id !== $student->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string',
            'face_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'id_photo' => 'nullable|image|mimes:jpeg,jpg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('face_photo')) {
            if ($pickupPerson->face_photo) {
                Storage::disk('public')->delete($pickupPerson->face_photo);
            }
            $validated['face_photo'] = $request->file('face_photo')->store('pickup-people/faces', 'public');
        }

        if ($request->hasFile('id_photo')) {
            if ($pickupPerson->id_photo) {
                Storage::disk('public')->delete($pickupPerson->id_photo);
            }
            $validated['id_photo'] = $request->file('id_photo')->store('pickup-people/ids', 'public');
        }

        $pickupPerson->update($validated);

        return redirect()->route('parent.pickup-people.index', $student)
            ->with('success', 'Persona autorizada actualizada exitosamente.');
    }

    public function destroy(Student $student, PickupPerson $pickupPerson)
    {
        $this->authorize('view', $student);

        if ($pickupPerson->student_id !== $student->id) {
            abort(404);
        }

        if ($pickupPerson->face_photo) {
            Storage::disk('public')->delete($pickupPerson->face_photo);
        }

        if ($pickupPerson->id_photo) {
            Storage::disk('public')->delete($pickupPerson->id_photo);
        }

        $pickupPerson->delete();

        return redirect()->route('parent.pickup-people.index', $student)
            ->with('success', 'Persona autorizada eliminada exitosamente.');
    }
}
