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
        $announcements = Announcement::query()
            ->where('teacher_id', auth()->id())
            ->latest()
            ->paginate(15);

        $mySubjects = Subject::where('teacher_id', auth()->id())->count();

        $pendingAssignments = Assignment::whereHas('subject', function ($query) {
            $query->where('teacher_id', auth()->id());
        })->where('due_date', '>=', now())->count();

        $totalAnnouncements = Announcement::where('teacher_id', auth()->id())->count();

        return view('teacher.announcements.index', compact(
            'announcements',
            'mySubjects',
            'pendingAssignments',
            'totalAnnouncements'
        ));
    }
}
