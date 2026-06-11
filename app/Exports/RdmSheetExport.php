<?php

namespace App\Exports;

use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RdmSheetExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected TeacherSubject $teacherSubject;
    protected Competency $competency;
    protected int $sheetNumber;

    public function __construct(TeacherSubject $teacherSubject, Competency $competency, int $sheetNumber)
    {
        $this->teacherSubject = $teacherSubject;
        $this->competency     = $competency;
        $this->sheetNumber    = $sheetNumber;
    }

    public function title(): string
    {
        return (string) $this->sheetNumber;
    }

    public function array(): array
    {
        $gradeName   = $this->teacherSubject->grade->name   ?? '-';
        $subjectName = $this->teacherSubject->subject->name ?? '-';

        // Ambil siswa beserta nilainya, eager-load studentRdm
        $studentCompetencies = StudentCompetency::with(['student', 'student.studentRdm'])
            ->where('teacher_subject_id', $this->teacherSubject->id)
            ->where('competency_id', $this->competency->id)
            ->get();

        // Kolom: A=No | B=RDM_ID(hidden) | C=NIS | D=NISN | E=Nama | F=Nilai
        $rows = [
            // Row 1: Judul
            ['Nilai Sumatif-' . $gradeName . '-' . $subjectName, null, null, null, null, null],
            // Row 2: Nama & Kelas
            ['Nama', null, $this->sheetNumber, 'Kelas/Mapel:', $gradeName . '/' . $subjectName . 'Array', null],
            // Row 3: Materi
            ['Materi', $this->competency->description, null, null, null, null],
            // Row 4: Kosong
            [null, null, null, null, null, null],
            // Row 5: KKTP
            ['KKTP', null, $this->competency->passing_grade, null, null, null],
            // Row 6: Header tabel (kolom B = RDM ID, akan disembunyikan)
            ['No', 'RDM ID', 'NIS', 'Nisn', 'Nama', 'Nilai'],
        ];

        // Row 7+: Data siswa
        $no = 1;
        foreach ($studentCompetencies as $sc) {
            $student = $sc->student;
            $rdmId   = $student?->studentRdm?->rdm_id ?? '-';

            $rows[] = [
                $no++,
                $rdmId,               // Kolom B: RDM ID (hidden)
                $student->nis  ?? '-',
                $student->nisn ?? '-',
                $student->name ?? '-',
                $sc->score     ?? 0,
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 50, // RDM ID – akan disembunyikan
            'C' => 10,
            'D' => 16,
            'E' => 32,
            'F' => 8,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $totalRows = count($this->array());

        // ── Sembunyikan kolom B (RDM ID) ─────────────────────────────────
        $sheet->getColumnDimension('B')->setVisible(false);

        // ── Merge cells ──────────────────────────────────────────────────
        $sheet->mergeCells('A1:F1'); // Judul
        $sheet->mergeCells('B3:F3'); // Materi value
        $sheet->mergeCells('E2:F2'); // Kelas/Mapel value

        // ── Row 1: Judul ─────────────────────────────────────────────────
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ── Row 3: Deskripsi Materi ──────────────────────────────────────
        $sheet->getStyle('B3')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_TOP,
                'wrapText'   => false,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // ── Row 2–5: Info rows background kuning ─────────────────────────
        $yellowFill = [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
        ];
        foreach (['A2:F2', 'A3:F3', 'A4:F4', 'A5:F5'] as $range) {
            $sheet->getStyle($range)->applyFromArray($yellowFill);
        }

        // Label bold
        foreach (['A2', 'A3', 'A5', 'D2'] as $cell) {
            $sheet->getStyle($cell)->applyFromArray(['font' => ['bold' => true]]);
        }

        // ── Row 6: Header tabel — abu gelap, teks putih ──────────────────
        $sheet->getStyle('A6:F6')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '595959'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '404040'],
                ],
            ],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(18);

        // ── Row 7+: Data siswa ───────────────────────────────────────────
        if ($totalRows >= 7) {
            $sheet->getStyle('A7:F' . $totalRows)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'BFBFBF'],
                    ],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Kolom No: center
            $sheet->getStyle('A7:A' . $totalRows)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Kolom Nilai: kuning + center
            $sheet->getStyle('F7:F' . $totalRows)->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFF00'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Alternating row: baris ganjil abu muda (seperti gambar)
            for ($row = 7; $row <= $totalRows; $row++) {
                if ($row % 2 !== 0) {
                    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D9D9D9'],
                        ],
                    ]);
                }
            }
        }

        return [];
    }
}
