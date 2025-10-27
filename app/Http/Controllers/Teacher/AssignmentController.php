<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index()
    {
        $teacher = auth()->user();
        $mySubjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->pluck('id');

        $activeAssignments = Assignment::whereIn('subject_id', $mySubjects)
            ->where('is_active', true)
            ->where('due_date', '>=', now()->toDateString())
            ->with('subject')
            ->latest('due_date')
            ->paginate(10);

        return view('teacher.assignments.index', compact('activeAssignments'));
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        $teacher = auth()->user();
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->with('gradeSection')
            ->get()
            ->sortBy(function ($subject) {
                return $subject->gradeSection?->name ?? $subject->grade_level;
            });

        return view('teacher.assignments.create', compact('subjects'));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(StoreAssignmentRequest $request)
    {
        $teacher = auth()->user();
        $validated = $request->validated();
        $validated['teacher_id'] = $teacher->id;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('assignments', 'public');
            $validated['attachment_path'] = $path;
            $validated['attachment_type'] = $this->getAttachmentType($file);
        }

        Assignment::create($validated);

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Tarea creada exitosamente');
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);

        return view('teacher.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $teacher = auth()->user();
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->where('school_year_id', function ($query) {
                $query->select('id')
                    ->from('school_years')
                    ->where('is_active', true)
                    ->limit(1);
            })
            ->with('gradeSection')
            ->get()
            ->sortBy(function ($subject) {
                return $subject->gradeSection?->name ?? $subject->grade_level;
            });

        return view('teacher.assignments.edit', compact('assignment', 'subjects'));
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(StoreAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($assignment->attachment_path) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }

            $file = $request->file('attachment');
            $path = $file->store('assignments', 'public');
            $validated['attachment_path'] = $path;
            $validated['attachment_type'] = $this->getAttachmentType($file);
        }

        $assignment->update($validated);

        return redirect()->route('teacher.assignments.show', $assignment)
            ->with('success', 'Tarea actualizada exitosamente');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        // Delete attachment if exists
        if ($assignment->attachment_path) {
            Storage::disk('public')->delete($assignment->attachment_path);
        }

        $assignment->delete();

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Tarea eliminada exitosamente');
    }

    /**
     * Download assignment attachment.
     */
    public function download(Assignment $assignment)
    {
        if (! $assignment->attachment_path || ! Storage::disk('public')->exists($assignment->attachment_path)) {
            return redirect()->back()->with('error', 'El archivo no existe');
        }

        return Storage::disk('public')->download($assignment->attachment_path);
    }

    /**
     * Get attachment type based on MIME type.
     */
    private function getAttachmentType($file): string
    {
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        }

        return 'file';
    }
}
