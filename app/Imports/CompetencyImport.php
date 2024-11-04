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

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!is_null($row['kode']) && !is_null($row['deskripsi']) && !is_null($row['kkm'])) {

                Competency::updateOrCreate([
                    'id' => $row['id'],
                ], [
                    'teacher_subject_id' => $row['teacher_subject_id'],
                    'code' => $row['kode'],
                    'description' => $row['deskripsi'],
                    'passing_grade' => $row['kkm'],
                ]);
            }
        }
    }

    public function headingRow(): int
    {
        return 10;
    }
}
