<?php

namespace App\Imports;

use App\Models\Competency;
use App\Models\Student;
use App\Models\StudentCompetency;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class RdmSumatifImport extends StringValueBinder implements WithCalculatedFormulas, WithCustomValueBinder
{
    protected $teacher_subject_id;

    public function __construct($teacher_subject_id)
    {
        $this->teacher_subject_id = $teacher_subject_id;
    }

    /**
     * Proses import file SAS/PAS dari RDM.
     * Struktur file (1 sheet):
     *   Row 0  : Judul
     *   Row 1  : Nama / Kelas/Mapel
     *   Row 2  : Materi (biasanya kosong untuk SAS)
     *   Row 3-4: Kosong
     *   Row 5  : Header (No, ID Siswa, NIS, Nisn, Nama, Nilai)
     *   Row 6+ : Data siswa
     *
     * Kolom 4 (Nisn) digunakan untuk mencocokkan dengan tabel Student.
     */
    public function processImport(string $file): array
    {
        $collection = Excel::toCollection($this, $file, 'public');

        $sheet = $collection->first();

        if (! $sheet || count($sheet) < 6) {
            return ['imported' => 0, 'skipped' => 0, 'error' => 'Format file tidak valid.'];
        }

        // Cari kompetensi "Akhir Semester" milik teacher_subject ini
        $competency = Competency::where('teacher_subject_id', $this->teacher_subject_id)
            ->whereRaw('UPPER(description) LIKE ?', ['%AKHIR SEMESTER%'])
            ->first();

        if (! $competency) {
            return ['imported' => 0, 'skipped' => 0, 'error' => 'Kompetensi "Akhir Semester" tidak ditemukan untuk mata pelajaran ini.'];
        }

        $imported = 0;
        $skipped = 0;

        // Data siswa dimulai dari Row 6 (index 6)
        for ($i = 6; $i < count($sheet); $i++) {
            $row = $sheet[$i];
            $nisn = $row[3] ?? null;
            $nilai = $row[5] ?? null;

            if (empty($nisn) || is_null($nilai)) {
                $skipped++;

                continue;
            }

            $student = Student::where('nisn', $nisn)->first();

            if (! $student) {
                $skipped++;

                continue;
            }

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

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'error' => null,
        ];
    }
}
