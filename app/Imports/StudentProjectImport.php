<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentProjectImport implements ToCollection, WithHeadingRow
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
                'student_id' => $row['student_id'],
                'project_target_id' => $row['project_target_id'],
                'score' => $row['score'],
            ];
        }
        return $data;
    }

    public function headingRow(): int
    {
        return 13;
    }
}
