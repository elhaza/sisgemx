<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // Admins can see all announcements, teachers only see their own
        $announcements = Announcement::query()
            ->when(! $isAdmin, function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->latest()
            ->paginate(15);

        $mySubjects = $isAdmin
            ? Subject::count()
            : Subject::where('teacher_id', $user->id)->count();

        $pendingAssignments = $isAdmin
            ? Assignment::where('due_date', '>=', now())->count()
            : Assignment::whereHas('subject', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->where('due_date', '>=', now())->count();

        $totalAnnouncements = $isAdmin
            ? Announcement::count()
            : Announcement::where('teacher_id', $user->id)->count();

        return view('teacher.announcements.index', compact(
            'announcements',
            'mySubjects',
            'pendingAssignments',
            'totalAnnouncements'
        ));
    }

    public function create(): View
    {
        return view('teacher.announcements.create');
    }

    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'nullable|array',
        ]);

        $validated['teacher_id'] = auth()->id();

        Announcement::create($validated);

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Anuncio creado exitosamente.');
    }

    public function show(Announcement $announcement): View
    {
        $this->authorize('view', $announcement);

        return view('teacher.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement): View
    {
        $this->authorize('update', $announcement);

        return view('teacher.announcements.edit', compact('announcement'));
    }

    public function update(Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $announcement);

        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'nullable|array',
        ]);

        $announcement->update($validated);

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Anuncio actualizado exitosamente.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Anuncio eliminado exitosamente.');
    }
}
