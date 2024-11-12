<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentCompetencyQuranImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'academic_year_id' => $row['academic_year_id'],
                'quran_grade_id' => $row['quran_grade_id'],
                'student_id' => $row['student_id'],
                'competency_quran_id' => $row['competency_quran_id'],
                'score' => $row['score'],
            ];
        }
    }

    public function headingRow(): int
    {
        return 13;
    }
}
