<?php

namespace App\Imports;

use App\Models\Transcript;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TranscriptImport implements ToCollection, WithHeadingRow
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
                    'written_exam' => $row['ujian_tulis'],
                    'practical_exam' => $row['ujian_praktek'],
                ]);
            }
        }
    }

    public function headingRow(): int
    {
        return 10;
    }
}
