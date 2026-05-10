<?php

namespace App\Services;

use App\Models\TeacherSubject;
use App\Enums\CurriculumEnum;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Protection;
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
                $identitas[9] = ['Kompetensi', ': (' . $competency->code . ') ', $competency->description];
                $identitas[10] = ['Keterampilan', ': (' . $competency->code_skill . ') ', $competency->description_skill];
            }

            $sheet->fromArray($identitas);

            // data header
            $data = [];
            $data[] = [
                'nis',
                'nama siswa',
                'score',
                'score_skill',
                'teacher_subject_id',
                'student_id',
                'competency_id',
            ];

            foreach ($competency->studentCompetency as $studentCompetency) {
                $data[] = [
                    $studentCompetency->student->nis,
                    $studentCompetency->student->name,
                    $studentCompetency->score,
                    $studentCompetency->score_skill,
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
            
            // jika k13
            if ($teacherSubject->teacherGrade->curriculum === CurriculumEnum::K13->value) {
                $sheet->getStyle('D14:D' . $rowStudent)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
            }

            // jika ujian
            if ($is_exam) {
                // proteksi cell D
                $sheet->getStyle('D13:D' . $rowStudent)->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
                // warna font cell D putih
                $sheet->getStyle('D13:D' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');
            }

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

                // jika k13
                if ($teacherSubject->teacherGrade->curriculum === CurriculumEnum::K13->value || $is_exam) {
                    $validation = $sheet->getCell('D' . $i)->getDataValidation();
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
            }

            // berikan warna putih pada font pada cell D jika bukan k13
            if ($teacherSubject->teacherGrade->curriculum !== CurriculumEnum::K13->value) {
                $sheet->getStyle('D13:D' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');
            }

            // berikan warna putih pada text pada cell E, F, G
            $sheet->getStyle('E13:G' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');

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
}
