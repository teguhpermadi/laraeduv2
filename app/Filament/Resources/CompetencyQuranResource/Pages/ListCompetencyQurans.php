<?php

namespace App\Filament\Resources\CompetencyQuranResource\Pages;

use App\Filament\Resources\CompetencyQuranResource;
use App\Imports\CompetencyQuranImport;
use App\Models\TeacherQuranGrade;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListCompetencyQurans extends ListRecords
{
    protected static string $resource = CompetencyQuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download')
                ->slideOver()
                ->closeModalByClickingAway(false)
                ->form([
                    Select::make('teacher_quran_grade_id')
                        ->label(__('competency-quran.teacher_quran_grade_id'))
                        ->options(TeacherQuranGrade::myQuranGrade()->get()->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->quranGrade->name,
                            ];
                        })->pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function ($data) {
                    return $this->export($data['teacher_quran_grade_id']);
                }),
            Actions\Action::make('upload')
                ->slideOver()
                ->closeModalByClickingAway(false)
                ->form([
                    FileUpload::make('file')
                        ->directory('uploads')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/x-excel'])
                        ->preserveFilenames()
                        ->required()
                ])
                ->action(function (array $data) {
                    Excel::import(new CompetencyQuranImport, storage_path('/app/public/' . $data['file']));
                }),
        ];
    }

    // buatkan tab berdasarkan quran grade yang dimiliki oleh teacher
    public function getTabs(): array
    {
        $quranGrades = TeacherQuranGrade::myQuranGrade()->with('quranGrade')->get();
        $tabs = [];
        foreach ($quranGrades as $quranGrade) {
            $tabs[$quranGrade->id] = Tab::make($quranGrade->quranGrade->name)
                ->modifyQueryUsing(function(Builder $query) use ($quranGrade){
                    $query->where('teacher_quran_grade_id', $quranGrade->id);
                });
        }

        return $tabs;
    }

    public function export($teacher_quran_grade_id)
    {
        $teacherQuranGrade = TeacherQuranGrade::find($teacher_quran_grade_id);

        $academicYear = $teacherQuranGrade->academicYear;
        $teacher = $teacherQuranGrade->teacher;
        $quranGrade = $teacherQuranGrade->quranGrade;
        $competencies = $teacherQuranGrade->competencyQuran;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->getSheet(0);

        // identitas
        // identitas
        $judulIdentitas = [
            ['Identitas pelajaran'],
            [null],
            ['Tahun Akademik'],
            ['Semester'],
            ['Nama Guru'],
            ['Mata Pelajaran'],
            ['Kelas Quran'],
        ];

        $sheet->fromArray($judulIdentitas, null, 'C1');

        $identitas = [
            [': ' . $academicYear->year],
            [': ' . $academicYear->semester],
            [': ' . $teacher->name],
            [': Mengaji'],
            [': ' . $quranGrade->name],
        ];

        $sheet->fromArray($identitas, null, 'D3');

        // Membuat lembar pertama
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Competency');

        $sheet1->setCellValue('A10', 'teacher_quran_grade_id');
        $sheet1->setCellValue('B10', 'id');
        $sheet1->setCellValue('C10', 'kode');
        $sheet1->setCellValue('D10', 'deskripsi');
        $sheet1->setCellValue('E10', 'kkm');

        $row = 11;
        foreach ($competencies as $competency) {
            $sheet1->setCellValue('A' . $row, $teacherQuranGrade->id);
            $sheet1->setCellValue('B' . $row, $competency->id);
            $sheet1->setCellValue('C' . $row, $competency->code);
            $sheet1->setCellValue('D' . $row, $competency->description);
            $sheet1->setCellValue('E' . $row, $competency->passing_grade);
            $row++;
        }

        for ($i = $row; $i < 50; $i++) {
            $sheet1->setCellValue('A' . $row, $teacherQuranGrade->id);
            $row++;
        }

        // setting width
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(50);

        // hide column A
        $sheet->getColumnDimension('A')->setVisible(false);
        $sheet->getColumnDimension('B')->setVisible(false);

        // bisa di edit
        $sheet->getStyle('C:E')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        // proteksi semua cell
        $sheet->getProtection()->setPassword('PhpSpreadsheet');
        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

        $writer = new Xlsx($spreadsheet);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx'); // <<< HERE
        $filename = "Kompetensi Quran " . $teacherQuranGrade->quranGrade->name . ".xlsx"; // <<< HERE
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $writer->save($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }
}
