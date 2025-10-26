<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentImportController extends Controller
{
    public function show()
    {
        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        return view('admin.students.import', compact('activeSchoolYear'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $activeSchoolYear = SchoolYear::where('is_active', true)->first();

        if (! $activeSchoolYear) {
            return redirect()
                ->route('admin.students.import')
                ->with('error', 'No hay ciclo escolar activo. Por favor, crea uno antes de importar estudiantes.')
                ->with('need_school_year', true);
        }

        try {
            // Increase execution time for import
            set_time_limit(300); // 5 minutes

            $import = new StudentsImport($activeSchoolYear);
            Excel::import($import, $request->file('file'));

            return redirect()
                ->route('admin.students.import')
                ->with('success', $import->getResultMessage());
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.students.import')
                ->with('error', 'Error al importar el archivo: '.$e->getMessage());
        }
    }

    public function createSchoolYear()
    {
        return view('admin.students.create-school-year');
    }

    public function storeSchoolYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'tuition_amount' => 'required|numeric|min:0.01',
        ]);

        // Deactivate any existing active school years
        SchoolYear::where('is_active', true)->update(['is_active' => false]);

        // Create new school year
        $schoolYear = SchoolYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.students.import')
            ->with('success', 'Ciclo escolar creado exitosamente.');
    }
}
