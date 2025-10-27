<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Subject;

class AnnouncementController extends Controller
{
    public function index()
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
}
