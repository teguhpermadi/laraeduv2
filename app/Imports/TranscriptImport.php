<?php

namespace App\Imports;

use App\Models\Transcript;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TranscriptImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // \dd($rows);
        foreach ($rows as $row) {
            if (!is_null($row['id'])) {
                Transcript::updateOrCreate([
                    'id' => $row['id'],
                ], [
                    'report_score' => $row['nilai_rapor'],
                    'written_exam' => !empty($row['ujian_tulis']) ? $row['ujian_tulis'] : 0,
                    'practical_exam' => !empty($row['ujian_praktek']) ? $row['ujian_praktek'] : 0,
                ]);
            }
        }
    }

    public function headingRow(): int
    {
        return 10;
    }
}
