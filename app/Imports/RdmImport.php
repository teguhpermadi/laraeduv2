<?php

namespace App\Imports;

use App\Models\Competency;
use App\Models\Student;
use App\Models\StudentCompetency;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class RdmImport extends StringValueBinder implements WithCalculatedFormulas, WithCustomValueBinder
{
    protected $teacher_subject_id;

    protected $academic_year_id;

    public function __construct($teacher_subject_id = null, $academic_year_id = null)
    {
        $this->teacher_subject_id = $teacher_subject_id;
        $this->academic_year_id = $academic_year_id;
    }

    public function processImport($file)
    {
        $collection = Excel::toCollection($this, $file, 'public');

        foreach ($collection as $sheetIndex => $sheet) {
            if (count($sheet) < 6) {
                continue;
            }

            $materi = $sheet[2][1] ?? null;
            $kktp = $sheet[4][1] ?? null;

            if (empty($materi)) {
                continue;
            }

            $competency = Competency::updateOrCreate(
                [
                    'teacher_subject_id' => $this->teacher_subject_id,
                    'description' => $materi,
                    'code' => 'Sumatif '.($sheetIndex + 1),
                ],
                [
                    'passing_grade' => $kktp ?? 0,
                    'half_semester' => false,
                ]
            );

            for ($i = 6; $i < count($sheet); $i++) {
                $row = $sheet[$i];
                if (! isset($row[3]) || empty($row[3])) {
                    continue;
                }

                $nisn = $row[3];
                $nilai = $row[5] ?? 0;

                $student = Student::where('nisn', $nisn)->first();

                if ($student) {
                    StudentCompetency::updateOrCreate(
                        [
                            'teacher_subject_id' => $this->teacher_subject_id,
                            'competency_id' => $competency->id,
                            'student_id' => $student->id,
                        ],
                        [
                            'score' => $nilai,
                        ]
                    );
                }
            }
        }
    }
}
