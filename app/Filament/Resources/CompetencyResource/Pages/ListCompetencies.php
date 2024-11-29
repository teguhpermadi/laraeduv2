<?php

namespace App\Filament\Resources\CompetencyResource\Pages;

use App\Filament\Resources\CompetencyResource;
use App\Imports\CompetencyImport;
use App\Models\Competency;
use App\Models\TeacherSubject;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Components\Tab;
// use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ListCompetencies extends ListRecords
{
    protected static string $resource = CompetencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('download')
                ->slideOver()
                ->form([
                    Select::make('teacher_subject_id')
                        ->label(__('competency.teacher_subject_id'))
                        ->options(
                            TeacherSubject::mySubject()->with('grade')->get()->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'code' => $item->subject->code . ' - ' . $item->grade->name,
                                ];
                            })->pluck('code', 'id')
                        )
                        ->required()
                ])
                ->action(function ($data) {
                    return $this->export($data['teacher_subject_id']);
                }),
            Action::make('upload')
                ->slideOver()
                ->form([
                    FileUpload::make('file')
                        ->directory('uploads')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/x-excel'])
                        ->preserveFilenames()
                        ->required()
                ])
                ->action(function (array $data) {
                    Excel::import(new CompetencyImport, storage_path('/app/public/' . $data['file']));
                }),
        ];
    }

    public function getTabs(): array
    {
        $subjects = TeacherSubject::with('competency', 'grade')->mySubject();
        $tabs = [];
        if($subjects->count() != 0){
            foreach ($subjects->get() as $subject) {
                $tabs[$subject->id] = Tab::make($subject->subject->code.'-'.$subject->grade->name)
                    ->modifyQueryUsing(function(Builder $query) use ($subject){
                        $competencyId = $subject->competency->pluck('id');
                        $query->whereIn('id',$competencyId);
                    })
                    ->badge(function() use ($subject){
                        $competencyId = $subject->competency->pluck('id');
                        return Competency::whereIn('id',$competencyId)->count();
                    })
                    ->badgeColor('success');
            }
        } else {
            $tabs = [
                '-' => Tab::make()
                    ->icon('heroicon-m-x-mark')
            ];
        }
        return $tabs;
    }

    public function export($teacher_subject_id)
    {
        $teacher_subject = TeacherSubject::with('academic', 'teacher', 'subject', 'grade.teacherGrade', 'competency')->find($teacher_subject_id);

        $academic = $teacher_subject->academic;
        $teacher = $teacher_subject->teacher;
        $grade = $teacher_subject->grade;
        $subject = $teacher_subject->subject;
        $competencies = $teacher_subject->competency;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->getSheet(0); // Indeks dimulai dari 0

        // identitas
        $judulIdentitas = [
            ['Identitas pelajaran'],
            [null],
            ['Tahun Akademik'],
            ['Semester'],
            ['Nama Guru'],
            ['Mata Pelajaran'],
            ['Kelas'],
        ];
        $sheet->fromArray($judulIdentitas, null, 'C1');

        $identitas = [
            [': ' . $academic->year],
            [': ' . $academic->semester],
            [': ' . $teacher->name],
            [': ' . $subject->name],
            [': ' . $grade->name],
        ];

        $sheet->fromArray($identitas, null, 'D3');

        // Membuat lembar pertama
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Competency');

        // cek kurikulum
        // dd($teacher_subject->grade->teacherGrade->curriculum->toArray());
        $sheet1->setCellValue('A10', 'teacher_subject_id');
        $sheet1->setCellValue('B10', 'id');
        $sheet1->setCellValue('C10', 'kode');
        $sheet1->setCellValue('D10', 'deskripsi');
        $sheet1->setCellValue('E10', 'kkm');

        $row = 11;
        foreach ($competencies as $competency) {
            $sheet1->setCellValue('A' . $row, $teacher_subject_id);
            $sheet1->setCellValue('B' . $row, $competency->id);
            $sheet1->setCellValue('C' . $row, $competency->description);
            $sheet1->setCellValueExplicit('C' . $row, $competency->code, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet1->setCellValue('D' . $row, $competency->description);
            $sheet1->setCellValue('E' . $row, $competency->passing_grade);
            $row++;
        }

        // tambahkan baris dengan kolom teacher_subject_id tambahan untuk yang baru
        for ($i = $row; $i < 50; $i++) {
            $sheet1->setCellValue('A' . $row, $teacher_subject_id);
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
        $filename = "Kompetensi " . $teacher_subject->subject->code . ' ' . $teacher_subject->grade->name . ".xlsx"; // <<< HERE
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $writer->save($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
        // return response()->download($file_path); // <<< HERE
    }
}
