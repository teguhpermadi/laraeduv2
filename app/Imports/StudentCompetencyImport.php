<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentCompetencyImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $data = [];
        foreach ($rows as $row) 
        {
            $data = [
                'teacher_subject_id' => $row['teacher_subject_id'],
                'student_id' => $row['student_id'],
                'competency_id' => $row['competency_id'],
                'score' => $row['score'],
                'score_skill' => $row['score_skill'],
            ];
        }

        // return $data;
    }

    public function headingRow(): int
    {
        return 13;
    }
}
