<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Display a listing of grades.
     */
    public function index()
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create()
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Display the specified grade.
     */
    public function show(Grade $grade)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Update the specified grade in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Remove the specified grade from storage.
     */
    public function destroy(Grade $grade)
    {
        return redirect()->route('teacher.dashboard');
    }
}
