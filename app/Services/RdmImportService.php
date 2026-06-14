<?php

namespace App\Services;

use App\Imports\RdmImport;
use App\Imports\RdmSumatifImport;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RdmImportService
{
    public function import(string $file, string $teacherSubjectId, ?string $academicYearId = null): array
    {
        $path = Storage::disk('public')->path($file);
        $spreadsheet = IOFactory::load($path);
        $sheetCount = $spreadsheet->getSheetCount();
        $spreadsheet->disconnectWorksheets();

        if ($sheetCount > 1) {
            $import = new RdmImport($teacherSubjectId, $academicYearId);
            $import->processImport($file);

            return ['success' => true, 'type' => 'rdm', 'error' => null, 'imported' => null, 'skipped' => null];
        }

        $import = new RdmSumatifImport($teacherSubjectId);
        $result = $import->processImport($file);

        return [
            'success' => empty($result['error']),
            'type' => 'rdm_sumatif',
            'error' => $result['error'] ?? null,
            'imported' => $result['imported'] ?? 0,
            'skipped' => $result['skipped'] ?? 0,
        ];
    }
}
