<?php

namespace App\Imports;

use App\Models\CompetencyQuran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompetencyQuranImport implements ToCollection, WithHeadingRow
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!is_null($row['kode']) && !is_null($row['deskripsi']) && !is_null($row['kkm'])) {

                CompetencyQuran::updateOrCreate([
                    'id' => $row['id'],
                ], [
                    'teacher_quran_grade_id' => $row['teacher_quran_grade_id'],
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
