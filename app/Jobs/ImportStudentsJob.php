<?php

namespace App\Jobs;

use App\Imports\StudentsImport;
use App\Models\SchoolYear;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudentsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $filePath,
        protected int $schoolYearId,
    ) {}

    public function handle(): void
    {
        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes

        $schoolYear = SchoolYear::findOrFail($this->schoolYearId);
        $import = new StudentsImport($schoolYear);
        Excel::import($import, $this->filePath);

        // Clean up the temporary file
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
