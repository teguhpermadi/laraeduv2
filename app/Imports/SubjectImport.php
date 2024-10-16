<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectImport implements ToCollection, WithHeadingRow
{
    use Importable, SkipsErrors;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $subject = Subject::create(
                    [
                        'name' => $row['nama_mata_pelajaran'],
                        'code' => $row['kode_mata_pelajaran'],
                    ],
                    [
                        'name' => $row['nama_mata_pelajaran'],
                        'code' => $row['kode_mata_pelajaran'],
                        'order' => $row['urutan_mata_pelajaran'],
                    ]
                );
            } catch (\Exception $e) {
                //throw $th;
                session()->push('import_errors', [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
