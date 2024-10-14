<?php

namespace App\Imports;

use App\Models\Grade;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GradeImport implements ToCollection, WithHeadingRow
{
    use Importable, SkipsErrors;
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $grade = Grade::updateOrCreate(
                    [
                        'name' => $row['nama_kelas'],
                        'grade' => $row['jenjang'],
                    ],
                    [
                        'name' => $row['nama_kelas'],
                        'grade' => $row['jenjang'],
                        'phase' => $row['fase'],
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
