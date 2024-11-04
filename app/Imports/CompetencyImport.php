<?php

namespace App\Imports;

use App\Models\Competency;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompetencyImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $teacherSubjectId;

    public function __construct($teacherSubjectId)
    {
        $this->teacherSubjectId = $teacherSubjectId['teacher_subject_id'];
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [
                'teacher_subject_id' => $this->teacherSubjectId,
                'code' => $row['kode'],
                'description' => $row['deskripsi'],
                'passing_grade' => $row['kkm'],
            ];
            Competency::create($data);
        }
    }
}
