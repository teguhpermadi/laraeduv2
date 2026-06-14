<?php

namespace App\Services;

use App\Enums\CategoryLegerEnum;
use App\Exports\RdmExport;
use App\Exports\RdmSumatifExport;
use App\Models\Competency;
use App\Models\TeacherSubject;
use ZipArchive;

class RdmExportZipService
{
    protected string $tempDir;

    protected string $outputDir;

    public function __construct()
    {
        $this->tempDir = storage_path('app/rdm-exports/temp');
        $this->outputDir = storage_path('app/rdm-exports');
    }

    public function export(string $academicYearId): string
    {
        $teacherSubjects = TeacherSubject::withoutGlobalScopes()
            ->where('academic_year_id', $academicYearId)
            ->with(['grade', 'subject', 'teacher'])
            ->get();

        if ($teacherSubjects->isEmpty()) {
            throw new \RuntimeException('Tidak ada data TeacherSubject untuk tahun akademik ini.');
        }

        $this->ensureDirectoriesExist();

        $excelFiles = [];
        $skippedSubjects = [];

        foreach ($teacherSubjects as $ts) {
            $gradeName = $ts->grade?->name ?? 'tanpa-kelas';
            $subjectName = $ts->subject?->name ?? 'tanpa-mapel';
            $teacherName = $ts->teacher?->name ?? 'tanpa-guru';

            $safeGrade = $this->sanitize($gradeName);
            $safeSubject = $this->sanitize($subjectName);
            $safeTeacher = $this->sanitize($teacherName);

            if ($this->hasRdmCompetencies($ts->id)) {
                $rdmFilename = "RDM_{$safeSubject}_{$safeGrade}_{$safeTeacher}.xlsx";
                $rdmPath = $this->tempDir.'/'.$rdmFilename;
                (new RdmExport($ts->id))->store($rdmFilename, 'rdm-exports-temp');
                $excelFiles[] = ['path' => $rdmPath, 'folder' => $safeTeacher];
            }

            if ($this->hasSumatifCompetencies($ts->id)) {
                $sumatifFilename = "RDM_Sumatif_{$safeSubject}_{$safeGrade}_{$safeTeacher}.xlsx";
                $sumatifPath = $this->tempDir.'/'.$sumatifFilename;
                (new RdmSumatifExport($ts->id))->store($sumatifFilename, 'rdm-exports-temp');
                $excelFiles[] = ['path' => $sumatifPath, 'folder' => $safeTeacher];
            }

            if (! $this->hasRdmCompetencies($ts->id) && ! $this->hasSumatifCompetencies($ts->id)) {
                $skippedSubjects[] = "{$subjectName} - {$gradeName} ({$teacherName})";
            }
        }

        if (empty($excelFiles)) {
            $reason = 'Tidak ada kompetensi yang cocok untuk diexport.';
            if (! empty($skippedSubjects)) {
                $reason .= ' Subject: '.implode(', ', $skippedSubjects);
            }
            throw new \RuntimeException($reason);
        }

        $zipFilename = 'rdm_export_'.now()->format('Ymd_His').'_'.uniqid().'.zip';
        $zipPath = $this->outputDir.'/'.$zipFilename;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Gagal membuat file ZIP.');
        }

        foreach ($excelFiles as $item) {
            if (file_exists($item['path'])) {
                $entry = $item['folder'].'/'.basename($item['path']);
                $zip->addFile($item['path'], $entry);
            }
        }

        $zip->close();

        $this->cleanupTempFiles(array_column($excelFiles, 'path'));

        return $zipFilename;
    }

    protected function hasRdmCompetencies(string $teacherSubjectId): bool
    {
        $excludedCodes = array_column(CategoryLegerEnum::cases(), 'value');

        return Competency::where('teacher_subject_id', $teacherSubjectId)
            ->whereNotIn('code', $excludedCodes)
            ->exists();
    }

    protected function hasSumatifCompetencies(string $teacherSubjectId): bool
    {
        return Competency::where('teacher_subject_id', $teacherSubjectId)
            ->where('code', CategoryLegerEnum::FULL_SEMESTER->value)
            ->exists();
    }

    protected function ensureDirectoriesExist(): void
    {
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
        if (! is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    protected function sanitize(string $value): string
    {
        $value = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $value);

        return str_replace(' ', '_', trim($value));
    }

    protected function cleanupTempFiles(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }
}
