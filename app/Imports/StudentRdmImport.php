<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentRdm;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class StudentRdmImport extends StringValueBinder implements ToModel, WithCustomValueBinder, WithHeadingRow
{
    public function model(array $row)
    {
        $student = Student::where('nisn', $row['nisn'])->first();
        if ($student) {
            StudentRdm::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'nis' => $row['nis'],
                    'nisn' => $row['nisn'],
                ],
                [
                    'rdm_id' => $row['rdm_id'],
                ]
            );
        }
    }
}
