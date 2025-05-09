<?php

namespace App\Filament\Resources\TranscriptResource\Pages;

use App\Enums\CategoryLegerEnum;
use App\Enums\TranscriptEnum;
use App\Filament\Pages\TranscriptWeightSettings;
use App\Filament\Resources\TranscriptResource;
use App\Filament\Resources\TranscriptResource\Widgets\TranscriptDataset1Widget;
use App\Filament\Resources\TranscriptResource\Widgets\TranscriptDataset2Widget;
use App\Filament\Resources\TranscriptResource\Widgets\TranscriptWidget;
use App\Helpers\IdHelper;
use App\Imports\TranscriptImport;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\TeacherSubject;
use App\Models\Transcript;
use App\Settings\TranscriptWeight;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Database\Eloquent\Builder;

class ListTranscripts extends ListRecords
{
    protected static string $resource = TranscriptResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TranscriptWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('preview')
                ->label('Preview')
                ->url(fn() => route('transcript-preview'))
                ->openUrlInNewTab(),
            Action::make('download')
                ->label('Download')
                ->modalWidth(MaxWidth::Medium)
                ->closeModalByClickingAway(false)
                ->form([
                    Select::make('teacher_subject_id')
                        ->label('Teacher Subject')
                        ->options(Grade::with('teacherSubject')->where('grade', 6)->first()->teacherSubject->pluck('subject.name', 'id'))
                        ->required()
                ])
                ->action(function ($data) {
                    // dd($data);
                    $transcripts = Transcript::where('teacher_subject_id', $data['teacher_subject_id'])->get();
                    if ($transcripts->isEmpty()) {
                        Notification::make()
                            ->title('Failed')
                            ->danger()
                            ->body('Sync data first!')
                            ->send();
                    } else {
                        return $this->download($data['teacher_subject_id']);
                    }
                }),
            Action::make('upload')
                ->label('Upload')
                ->modalWidth(MaxWidth::Medium)
                ->closeModalByClickingAway(false)
                ->form([
                    FileUpload::make('file')
                        ->directory('uploads')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/x-excel'])
                        ->preserveFilenames()
                        ->required()
                ])
                ->action(function ($data) {
                    Excel::import(new TranscriptImport, storage_path('/app/public/' . $data['file']));
                }),
            Action::make('sycn')
                ->label('Sync')
                ->slideOver()
                ->form([
                    Select::make('grade_id')
                        ->label('Grade')
                        ->live()
                        ->options(Grade::all()->pluck('name', 'id'))
                        ->required(),
                    Select::make('academic_year_id')
                        ->label('Academic Year')
                        ->live()
                        ->multiple()
                        ->options(AcademicYear::all()->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'year' => $item->year . ' - ' . $item->semester,
                            ];
                        })->pluck('year', 'id'))
                        ->required(),
                    Select::make('student_ids')
                        ->reactive()
                        ->multiple()
                        ->options(function (callable $get) {
                            $studentGrade = StudentGrade::where('grade_id', $get('grade_id'))
                                ->where('academic_year_id', $get('academic_year_id'))
                                ->with('student')
                                ->get();

                            return $studentGrade->pluck('student.name', 'student.id');
                        })
                        ->required()
                        ->hintAction(
                            fn(Select $component) => ActionsAction::make('select all')
                                ->action(function (callable $get) use ($component) {
                                    $studentGrade = StudentGrade::where('grade_id', $get('grade_id'))
                                        ->where('academic_year_id', $get('academic_year_id'))
                                        ->get();

                                    return $component->state($studentGrade->pluck('student_id')->toArray());
                                })
                        ),
                ])
                ->action(function ($data) {
                    $this->sync($data);

                    Notification::make()
                        ->title('Sync Success')
                        ->success()
                        ->send();
                }),
            Action::make('recalculate')
                ->label('Re-Calculate')
                ->modalWidth(MaxWidth::Medium)
                ->closeModalByClickingAway(false)
                ->form([
                    Select::make('dataset')
                        ->label('Dataset')
                        ->options(function () {
                            $transcriptWeight = app(TranscriptWeight::class);
                            return [
                                'dataset1' => 'Rapor ' . $transcriptWeight->weight_report1 . '%, Tulis ' . $transcriptWeight->weight_written_exam1 . '%, Praktek ' . $transcriptWeight->weight_practical_exam1 . '%',
                                'dataset2' => 'Rapor ' . $transcriptWeight->weight_report2 . '%, Tulis ' . $transcriptWeight->weight_written_exam2 . '%, Praktek ' . $transcriptWeight->weight_practical_exam2 . '%',
                            ];
                        })
                        ->required(),
                ])
                ->action(function ($data) {
                    $dataset = $data['dataset'];
                    $transcriptWeightSetting = app(TranscriptWeight::class);

                    $weight_report = 0;
                    $weight_written_exam = 0;
                    $weight_practical_exam = 0;

                    switch ($dataset) {
                        case 'dataset1':
                            $weight_report = $transcriptWeightSetting->weight_report1;
                            $weight_written_exam = $transcriptWeightSetting->weight_written_exam1;
                            $weight_practical_exam = $transcriptWeightSetting->weight_practical_exam1;
                            break;
                        case 'dataset2':
                            $weight_report = $transcriptWeightSetting->weight_report2;
                            $weight_written_exam = $transcriptWeightSetting->weight_written_exam2;
                            $weight_practical_exam = $transcriptWeightSetting->weight_practical_exam2;
                            break;
                    }

                    $transcripts = Transcript::all();

                    // re-calculate average all transcript by dataset choice
                    foreach ($transcripts as $transcript) {
                        $transcript->average_score = $transcript->calculateAverage($weight_report, $weight_written_exam, $weight_practical_exam);
                        $transcript->save();
                    }

                    // save transcript weight setting by dataset choice
                    $transcriptWeightSetting->weight_report = $weight_report;
                    $transcriptWeightSetting->weight_written_exam = $weight_written_exam;
                    $transcriptWeightSetting->weight_practical_exam = $weight_practical_exam;
                    $transcriptWeightSetting->save();

                    Notification::make()
                        ->title('Recalculate Success')
                        ->success()
                        ->send();
                })
        ];
    }

    public function getTabs(): array
    {
        $grade = Grade::where('grade', 6)->first();
        $teacherSubjects = TeacherSubject::where('academic_year_id', session('academic_year_id'))
        ->where('grade_id', $grade->id)
        ->get();

        $tabs = [];

        foreach ($teacherSubjects as $teacherSubject) {
            $tabs[$teacherSubject->id] = Tab::make($teacherSubject->subject->code)
                ->modifyQueryUsing(function (Builder $query) use ($teacherSubject) {
                    return $query->where('teacher_subject_id', $teacherSubject->id);
                });
        }

        return $tabs;
    }

    public function sync($data)
    {
        $studentIds = $data['student_ids'];
        $academicYearIds = $data['academic_year_id'];
        $gradeId = $data['grade_id'];

        $students = Student::with(['leger' => function ($query) use ($academicYearIds) {
            $query->with('academicYear');
            $query->select('id', 'student_id', 'subject_id', 'academic_year_id', 'teacher_subject_id', 'score', 'score_skill');
            $query->where('category', CategoryLegerEnum::FULL_SEMESTER->value);
            $query->whereIn('academic_year_id', $academicYearIds);
        }])
            ->whereIn('id', $studentIds)
            ->orderBy('id', 'asc')
            ->get();

        // dd($students);

        $data = [];
        foreach ($students as $student) { // setiap siswa
            $legers = $student->leger->groupBy('subject.id');

            foreach ($legers as $i => $leger) { // setiap mata pelajaran
                $teacherSubject = TeacherSubject::where('academic_year_id', session()->get('academic_year_id'))
                    ->where('subject_id', $i)
                    ->where('grade_id', $gradeId)
                    ->first();

                $param = $student->id . $i;
                $data[] = [
                    'id' => IdHelper::deterministicUlidLike($param),
                    'student_id' => $student->id,
                    'academic_year_id' => session()->get('academic_year_id'),
                    'teacher_subject_id' => $teacherSubject->id,
                    'subject_id' => $i,
                    'report_score' => $leger->average('score'),
                    'written_exam' => null,
                    'practical_exam' => null,
                    'average_score' => 0,
                ];

                // Transcript::updateOrCreate(['id' => $data['id']], $data); // dd($data);
            }
        }

        // dd($data);
        Transcript::upsert($data, uniqueBy: ['id'], update: ['report_score', 'written_exam', 'practical_exam', 'average_score']);
    }

    public function download($teacher_subject_id)
    {
        $transcripts = Transcript::where('teacher_subject_id', $teacher_subject_id)->orderBy('student_id', 'asc')->get();
        $teacher_subject = TeacherSubject::with('academic', 'teacher', 'subject', 'grade.teacherGrade', 'competency')->find($teacher_subject_id);

        $academic = $teacher_subject->academic;
        $teacher = $teacher_subject->teacher;
        $grade = $teacher_subject->grade;
        $subject = $teacher_subject->subject;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->getSheet(0); // Indeks dimulai dari 0

        // identitas
        $judulIdentitas = [
            ['TRANSKRIP NILAI IJAZAH'],
            [null],
            ['Tahun Akademik'],
            ['Semester'],
            ['Nama Guru'],
            ['Mata Pelajaran'],
            ['Kelas'],
        ];
        $sheet->fromArray($judulIdentitas, null, 'B1');

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
        $sheet1->setTitle('Transcript');

        $sheet->setCellValue('A10', 'id');
        $sheet->setCellValue('B10', 'nisn');
        $sheet->setCellValue('C10', 'nama siswa');
        $sheet->setCellValue('D10', 'nilai rapor');
        $sheet->setCellValue('E10', 'ujian tulis');
        $sheet->setCellValue('F10', 'ujian praktek');

        $row = 11;
        foreach ($transcripts as $transcript) {
            $sheet1->setCellValue('A' . $row, $transcript->id);
            $sheet1->setCellValue('B' . $row, $transcript->student->nisn);
            $sheet1->setCellValue('C' . $row, $transcript->student->name);
            $sheet1->setCellValue('D' . $row, $transcript->report_score);
            $sheet1->setCellValue('E' . $row, $transcript->written_exam);
            $sheet1->setCellValue('F' . $row, $transcript->practical_exam);
            $row++;
        }

        // setting width
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);

        // hide column A
        $sheet->getColumnDimension('A')->setVisible(false);

        // bisa di edit
        $sheet->getStyle('D:F')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        // proteksi semua cell
        $sheet->getProtection()->setPassword('PhpSpreadsheet');
        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

        $writer = new Xlsx($spreadsheet);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx'); // <<< HERE
        $filename = "Transkrip Nilai " . $teacher_subject->subject->code . ' ' . $teacher_subject->grade->name . ".xlsx"; // <<< HERE
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $writer->save($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }
}
