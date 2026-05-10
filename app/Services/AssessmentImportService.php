<?php

namespace App\Services;

use App\Models\StudentCompetency;
use App\Imports\StudentCompetencyImport;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentImportService
{
    /**
     * Import assessment data from Excel.
     *
     * @param string $filePath Relative path to the file in storage.
     * @return void
     */
    public function import(string $filePath): void
    {
        $fullPath = storage_path('app/public/' . $filePath);
        
        $studentCompetencies = Excel::toArray(new StudentCompetencyImport, $fullPath);

        foreach ($studentCompetencies as $row) {
            foreach ($row as $value) {
                StudentCompetency::where([
                    'teacher_subject_id' => $value['teacher_subject_id'],
                    'student_id' => $value['student_id'],
                    'competency_id' => $value['competency_id'],
                ])
                ->update([
                    'score' => $value['score'],
                    'score_skill' => $value['score_skill'],
                ]);
            }
        }
    }
}
