<?php

namespace App\Services;

use App\Models\TeacherSubject;
use App\Enums\CurriculumEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssessmentExportService
{
    /**
     * Export assessment data to Excel.
     *
     * @param int|string $teacherSubjectId
     * @return BinaryFileResponse
     */
    public function export($teacherSubjectId): BinaryFileResponse
    {
        $teacherSubject = TeacherSubject::with([
            'academic',
            'teacher',
            'grade.teacherGrade',
            'subject',
            'competency.studentCompetency.student',
        ])->findOrFail($teacherSubjectId);

        $academic = $teacherSubject->academic;
        $teacher = $teacherSubject->teacher;
        $grade = $teacherSubject->grade;
        $subject = $teacherSubject->subject;
        $competencies = $teacherSubject->competency;
        $countStudent = $teacherSubject->grade->studentGrade->count();

        // Inisialisasi spreadsheet
        $spreadsheet = new Spreadsheet();
        $countSheet = 0;

        // buat sheet berdasarkan banyaknya kompetensi
        foreach ($competencies as $competency) {
            // Jika ini bukan iterasi pertama, buat sheet baru
            if ($countSheet > 0) {
                $spreadsheet->createSheet();
            }
            
            // Mengambil sheet yang aktif (indeks $countSheet)
            $sheet = $spreadsheet->getSheet($countSheet);
            $sheet->setTitle('Sheet ' . ($competency->code));

            $is_exam = in_array($competency->code, ['TENGAH SEMESTER', 'AKHIR SEMESTER']);

            // identitas
            $identitas = [
                ['Identitas pelajaran'],
                [null],
                ['Nama Guru', ': ' . $teacher->name],
                ['Mata Pelajaran', ': ' . $subject->name],
                ['Kelas', ': ' . $grade->name],
                ['Tahun Akademik', ': ' . $academic->year],
                ['Semester', ': ' . $academic->semester],
            ];

            // jika bukan ujian
            if (!$is_exam) {
                $aspectLabel = $competency->aspect ? $competency->aspect->getLabel() : 'Pengetahuan';
                $identitas[9] = ['Kompetensi (' . $aspectLabel . ')', ': (' . $competency->code . ') ', $competency->description];
            }

            $sheet->fromArray($identitas);

            // data header
            $data = [];
            $data[] = [
                'nis',
                'nama siswa',
                'score',
                'teacher_subject_id',
                'student_id',
                'competency_id',
            ];

            foreach ($competency->studentCompetency as $studentCompetency) {
                $data[] = [
                    $studentCompetency->student->nis,
                    $studentCompetency->student->name,
                    $studentCompetency->score,
                    $studentCompetency->teacher_subject_id,
                    $studentCompetency->student_id,
                    $studentCompetency->competency_id,
                ];
            }

            $sheet->fromArray($data, null, 'A13', true);

            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);

            // count student
            $rowStudent = 13 + $countStudent;

            // bisa di edit
            $sheet->getStyle('C14:C' . $rowStudent)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            
            // proteksi semua cell
            $sheet->getProtection()->setPassword('PhpSpreadsheet');
            $sheet->getProtection()->setSheet(true);

            // validasi tiap-tiap cell
            for ($i = 14; $i <= $rowStudent; $i++) {
                $validation = $sheet->getCell('C' . $i)->getDataValidation();
                $validation->setType(DataValidation::TYPE_WHOLE);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Number is not allowed!');
                $validation->setPromptTitle('Allowed input');
                $validation->setPrompt('Only numbers between 0 and 100 are allowed.');
                $validation->setFormula1(0);
                $validation->setFormula2(100);
            }

            // berikan warna putih pada text pada cell D, E, F (sembunyikan ID database)
            $sheet->getStyle('D13:F' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');

            // Set active cell to C14
            $sheet->setSelectedCell('C14');
            
            $countSheet++;
        }

        // Set sheet pertama sebagai sheet aktif
        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = "nilai " . $teacherSubject->subject->name . ' ' . $teacherSubject->grade->name . ".xlsx";
        
        $directory = storage_path('app/public/downloads');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $file_path = $directory . '/' . $filename;
        $writer->save($file_path);

        return response()->download($file_path)->deleteFileAfterSend(true);
    }

    /**
     * Export assessment data to Excel with RDM layout.
     *
     * @param int|string $teacherSubjectId
     * @return BinaryFileResponse
     */
    public function exportToRdm($teacherSubjectId): BinaryFileResponse
    {
        $teacherSubject = TeacherSubject::with([
            'academic',
            'teacher',
            'grade.teacherGrade',
            'subject',
            'competency.studentCompetency.student',
        ])->findOrFail($teacherSubjectId);

        $academic = $teacherSubject->academic;
        $grade = $teacherSubject->grade;
        $subject = $teacherSubject->subject;
        $competencies = $teacherSubject->competency;

        $spreadsheet = new Spreadsheet();
        $countSheet = 0;

        foreach ($competencies as $competency) {
            if ($countSheet > 0) {
                $spreadsheet->createSheet();
            }
            
            $sheet = $spreadsheet->getSheet($countSheet);
            $sheet->setTitle($competency->code);

            // Row 1: Header
            $sheet->mergeCells('A1:F1');
            $sheet->setCellValue('A1', 'Template Nilai Sumatif');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Row 2: Metadata
            $sheet->setCellValue('A2', 'Nama');
            $sheet->setCellValue('B2', $competency->code);
            $sheet->setCellValue('D2', 'Kelas/Mapel:');
            $sheet->mergeCells('E2:F2');
            $sheet->setCellValue('E2', $grade->name . '/' . $subject->name);

            // Row 3-4: Materi
            $sheet->mergeCells('A3:A4');
            $sheet->setCellValue('A3', 'Materi');
            $sheet->mergeCells('B3:F4');
            $sheet->setCellValue('B3', $competency->description);
            $sheet->getStyle('B3')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

            // Row 5: KKTP
            $sheet->mergeCells('A5:B5');
            $sheet->setCellValue('A5', 'KKTP');
            $sheet->setCellValue('C5', $competency->passing_grade);

            // Row 6: Table Header
            $headers = ['No', 'ID Siswa', 'NIS', 'Nisn', 'Nama', 'Nilai'];
            $sheet->fromArray($headers, null, 'A6');

            // Data
            $data = [];
            $no = 1;
            $studentCompetencies = $competency->studentCompetency->sortBy(function($item) {
                return $item->student->name;
            });

            foreach ($studentCompetencies as $studentCompetency) {
                $data[] = [
                    $no++,
                    $studentCompetency->id,
                    $studentCompetency->student->nis,
                    $studentCompetency->student->nisn,
                    $studentCompetency->student->name,
                    $studentCompetency->score,
                ];
            }
            $sheet->fromArray($data, null, 'A7');

            // Styling
            $lastRow = 6 + count($data);

            // Background colors
            $greyStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFA6A6A6'],
                ],
                'font' => ['bold' => true],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ];

            $yellowStyle = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'],
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ];

            // Apply styles to metadata labels
            $sheet->getStyle('A2')->applyFromArray($greyStyle);
            $sheet->getStyle('D2')->applyFromArray($greyStyle);
            $sheet->getStyle('A3:A4')->applyFromArray($greyStyle);
            $sheet->getStyle('A5:B5')->applyFromArray($greyStyle);

            // Apply styles to metadata values
            $sheet->getStyle('B2')->applyFromArray($yellowStyle);
            $sheet->getStyle('E2:F2')->applyFromArray($yellowStyle);
            $sheet->getStyle('B3:F4')->applyFromArray($yellowStyle);
            $sheet->getStyle('C5')->applyFromArray($yellowStyle);

            // Apply styles to table header
            $sheet->getStyle('A6:F6')->applyFromArray($greyStyle);

            // Apply borders to data rows
            $sheet->getStyle('A7:F' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Apply yellow background to Nilai column
            $sheet->getStyle('F7:F' . $lastRow)->applyFromArray($yellowStyle);

            // Column widths
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(40);
            $sheet->getColumnDimension('F')->setWidth(10);

            // Protection
            $sheet->getProtection()->setPassword('PhpSpreadsheet');
            $sheet->getProtection()->setSheet(true);
            $sheet->getStyle('F7:F' . $lastRow)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

            $countSheet++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = "RDM_" . $teacherSubject->subject->name . '_' . $teacherSubject->grade->name . ".xlsx";
        
        $directory = storage_path('app/public/downloads');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $file_path = $directory . '/' . $filename;
        $writer->save($file_path);

        return response()->download($file_path)->deleteFileAfterSend(true);
    }
}
