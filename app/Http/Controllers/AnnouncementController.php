<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of all valid announcements.
     */
    public function index()
    {
        $today = now()->toDateString();

        // Get all valid announcements with pagination
        $announcements = Announcement::query()
            ->with('teacher')
            ->where(function ($query) use ($today) {
                // If no dates, always show
                $query->whereNull('valid_from')
                    ->whereNull('valid_until')
                    // Or if within valid date range
                    ->orWhere(function ($q) use ($today) {
                        $q->where(function ($subQuery) use ($today) {
                            $subQuery->whereNull('valid_from')
                                ->orWhere('valid_from', '<=', $today);
                        })
                            ->where(function ($subQuery) use ($today) {
                                $subQuery->whereNull('valid_until')
                                    ->orWhere('valid_until', '>=', $today);
                            });
                    });
            })
            ->latest()
            ->paginate(10);

        return view('announcements.index', [
            'announcements' => $announcements,
        ]);
    }
}
