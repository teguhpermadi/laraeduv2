<?php

namespace App\Imports;

use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\StudentRdm;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Facades\Excel;

class RdmSumatifImport implements WithCalculatedFormulas
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
     * Kolom 1 (ID Siswa) berisi RDM ID yang dicocokkan dengan StudentRdm.rdm_id
     * untuk menemukan student_id yang sesuai.
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
        $skipped  = 0;

        // Data siswa dimulai dari Row 6 (index 6)
        for ($i = 6; $i < count($sheet); $i++) {
            $row   = $sheet[$i];
            $rdmId = $row[1] ?? null;
            $nilai = $row[5] ?? null;

            if (empty($rdmId) || is_null($nilai)) {
                $skipped++;
                continue;
            }

            // Cari StudentRdm berdasarkan rdm_id
            $studentRdm = StudentRdm::where('rdm_id', $rdmId)->first();

            if (! $studentRdm) {
                $skipped++;
                continue;
            }

            // Update atau buat StudentCompetency
            StudentCompetency::updateOrCreate(
                [
                    'teacher_subject_id' => $this->teacher_subject_id,
                    'competency_id'      => $competency->id,
                    'student_id'         => $studentRdm->student_id,
                ],
                [
                    'score' => $nilai,
                ]
            );

            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped'  => $skipped,
            'error'    => null,
        ];
    }
}
