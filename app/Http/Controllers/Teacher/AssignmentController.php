<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index()
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create()
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Show the form for editing the specified assignment.
     */
    public function edit(Assignment $assignment)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Update the specified assignment in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(Assignment $assignment)
    {
        return redirect()->route('teacher.dashboard');
    }
}
