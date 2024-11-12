<?php

namespace App\Filament\Pages;

use App\Models\QuranGrade;
use App\Models\StudentQuranGrade;
use App\Models\TeacherQuranGrade;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Radio;
use App\Models\CompetencyQuran;
use App\Models\StudentCompetencyQuran;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\TableAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

use Filament\Tables\Actions\Action;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AssessmentQuran extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.assessment-quran';

    // navigation label
    protected static ?string $navigationLabel = 'Penilaian Quran';

    // navigation group
    protected static ?string $navigationGroup = 'Mengaji';

    public ?array $data = [];
    public $quranGrade = [];
    public $competencyQuran = [];
    public $quran_grade_id = -1, $competency_quran_id = -1;
    public $empty_state = [];

    public function mount(): void
    {
        $teacherQuranGrade = TeacherQuranGrade::myQuranGrade()->get();
        // ambil quran grade id dan simpan dalam $quranGrade
        $this->quranGrade = $teacherQuranGrade->pluck('quranGrade.name', 'id');

        // cek apakah ada student
        $students = TeacherQuranGrade::myQuranGrade()->with('studentQuranGrade')->get();

        if (count($students) == 0) {
            $this->empty_state['heading'] = 'Anda tidak memiliki murid.';
            $this->empty_state['desc'] = 'Silahkan hubungi Admin!';
        } else {
            $this->empty_state['heading'] = '';
            $this->empty_state['desc'] = '';
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas')
                    ->schema([
                        Select::make('quran_grade_id')
                            ->options($this->quranGrade)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                // ambil competency quran berdasarkan quran grade id
                                $competencyQuran = CompetencyQuran::where('teacher_quran_grade_id', $state)->get();
                                $this->competencyQuran = $competencyQuran->pluck('description', 'id');

                                $this->competency_quran_id = -1;
                                $this->resetTable();
                                // dd($this->competencyQuran);
                            }),
                        Radio::make('competency_quran_id')
                            ->options(function () {
                                return $this->competencyQuran;
                            })
                            ->live()
                            ->required()
                            ->afterStateUpdated(function () {
                                $this->resetTable();
                            }),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StudentCompetencyQuran::query()
                    // ->where('teacher_quran_grade_id', $this->quran_grade_id)
                    ->where('competency_quran_id', $this->competency_quran_id)
            )
            ->emptyStateHeading($this->empty_state['heading'])
            ->emptyStateDescription($this->empty_state['desc'])
            ->columns([
                TextColumn::make('student.name'),
                TextInputColumn::make('score'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([
                BulkAction::make('scoreAdjustment')
                    ->label('Atur Nilai')
                    ->color('warning')
                    ->form([
                        Fieldset::make()
                            ->schema([
                                TextInput::make('score_min')
                                    ->label(__('assessment.score_min'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100),
                                TextInput::make('score_max')
                                    ->label(__('assessment.score_max'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100),
                            ])
                            ->columns(2),
                    ])
                    ->action(function (Collection $records, $data) {
                        $this->scoreAdjustment($records, $data);
                    })
            ])
            ->headerActions([
                Action::make('reset')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->form([
                        Select::make('quran_grade_id')
                            ->options($this->quranGrade),
                    ])
                    ->action(function ($data) {
                        // dd($data);
                        $this->resetStudentCompetency($data['quran_grade_id']);
                    }),
                Action::make('download')
                    ->form([
                        Select::make('quran_grade_id')
                            ->options($this->quranGrade),
                    ])
                    ->action(function ($data) {
                        return $this->download($data['quran_grade_id']);
                    }),
                Action::make('upload')
                    ->form([
                        FileUpload::make('file')
                            ->directory('uploads')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/x-excel'])
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file) {
                                    return 'siswa.' . $file->getClientOriginalExtension();
                                }
                            )
                            ->required()
                    ])
                    ->action(function (array $data) {
                        // TODO: upload file
                    }),
                Action::make('leger')
                // ->url(route('filament.admin.pages.leger.{id}', $this->quran_grade_id)),
            ])
            ->deferLoading()
            ->striped()
            ->paginated(false);
    }

    public function scoreAdjustment($records, $data)
    {
        $scoreMin = (int) $data['score_min'];
        $scoreMax = (int) $data['score_max'];

        $original = collect();
        foreach ($records as $key) {
            $original->push([
                'id' => $key->id,
                'score' => $key->score,
            ]);
        }

        $originalScoreMin = (int) $original->min('score');
        $originalScoreMax = (int) $original->max('score');

        // score adjusment
        $original->map(function ($item) use ($scoreMin, $scoreMax, $originalScoreMin, $originalScoreMax, $data) {
            // apa yang dinilai
            $newScore = $scoreMin + (($item['score'] - $originalScoreMin) / ($originalScoreMax - $originalScoreMin) * ($scoreMax - $scoreMin));
            StudentCompetencyQuran::find($item['id'])
                ->update([
                    'score' => $newScore,
                ]);
        });
    }

    // reset student competency
    public function resetStudentCompetency($quran_grade_id)
    {
        // ambil semua student dari quran grade id
        $students = StudentQuranGrade::where('quran_grade_id', $quran_grade_id)->get()->pluck('student_id');

        if (count($students) == 0) {
            // hapus semua student competency berdasarkan quran grade id
            StudentCompetencyQuran::where('quran_grade_id', $quran_grade_id)->delete();
        }

        // get students
        $students = TeacherQuranGrade::myQuranGrade()->where('quran_grade_id', $quran_grade_id)->with('studentQuranGrade')->first();

        // dd($students->studentQuranGrade->toArray());

        // get competencies
        $competencies = CompetencyQuran::where('teacher_quran_grade_id', $quran_grade_id)->get();

        // dd($competencies->toArray());

        // create new student competency
        foreach ($students->studentQuranGrade as $student) {
            foreach ($competencies as $competency) {
                $data = [
                    // 'teacher_quran_grade_id' => $quran_grade_id,
                    'academic_year_id' => session('academic_year_id'),
                    'quran_grade_id' => $quran_grade_id,
                    'student_id' => $student->student_id,
                    'competency_quran_id' => $competency->id,
                ];

                StudentCompetencyQuran::updateOrCreate($data, ['score' => 0]);
            }
        }

        // notification
        Notification::make()
            ->title('Berhasil')
            ->body('Berhasil mereset nilai')
            ->success()
            ->send();
    }

    public function download($quran_grade_id)
    {
        // ambil teacher quran grade berdasarkan quran grade id
        $teacherQuranGrade = TeacherQuranGrade::myQuranGrade()->with('competencyQuran.studentCompetencyQuran.student')->find($quran_grade_id);

        // ambil semua student dari quran grade id
        $students = StudentQuranGrade::where('quran_grade_id', $quran_grade_id)->get();

        $academicYear = $teacherQuranGrade->academicYear;
        $teacher = $teacherQuranGrade->teacher;
        $quranGrade = $teacherQuranGrade->quranGrade;
        $competencyQuran = $teacherQuranGrade->competencyQuran;
        
        // inisialisasi spreadsheet
        $spreadsheet = new Spreadsheet();
        $countSheet = 0;

        // buat sheet berdasarkan banyaknya kompetensi
        foreach ($competencyQuran as $competency) {
            $spreadsheet->createSheet();
            $sheet = $spreadsheet->getSheet($countSheet);
            $sheet->setTitle('Sheet ' . $competency->code);

            // identitas
            $identitas = [
                ['Identitas Pelajaran'],
                [null],
                ['Nama Guru', ': ' . $teacher->name],
                ['Mata Pelajaran', ': ' . $quranGrade->name],
                ['Kelas', ': ' . $teacher->name],
                ['Tahun Akademik', ': ' . $academicYear->year],
                ['Semester', ': ' . $academicYear->semester],
                ['Kompetensi', ': (' . $competency->code . ') ', $competency->description],
            ];
            $sheet->fromArray($identitas, null, 'E1', true);

            $data = [];
            $data[] = [
                'academic_year_id',
                'quran_grade_id',
                'student_id',
                'competency_quran_id',
                'nis',
                'name',
                'score',
            ];

            foreach ($competency->studentCompetencyQuran as $studentCompetency) {
                $data[] = [
                    $academicYear->id,
                    $quran_grade_id,
                    $studentCompetency->student_id,
                    $competency->id,
                    $studentCompetency->student->nis,
                    $studentCompetency->student->name,
                    $studentCompetency->score,
                ];
            }

            $sheet->fromArray($data, null, 'A13', true);

            $countSheet++;

            $sheet->getColumnDimension('F')->setWidth(30);

            // hide column A B C D
            $sheet->getColumnDimension('A')->setVisible(false);
            $sheet->getColumnDimension('B')->setVisible(false);
            $sheet->getColumnDimension('C')->setVisible(false);
            $sheet->getColumnDimension('D')->setVisible(false);

            // count student
            $rowStudent = 13 + count($students);

            // bisa di edit
            $sheet->getStyle('G14:G' . $rowStudent)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

            // proteksi semua cell
            $sheet->getProtection()->setPassword('PhpSpreadsheet');
            $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

            // validasi tiap-tiap cell
            for ($i = 14; $i <= $rowStudent; $i++) {
                $validation = $sheet->getCell('F' . $i)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Number is not allowed!');
                $validation->setPromptTitle('Allowed input');
                $validation->setPrompt('Only numbers between 0 and 100 are allowed.');
                $validation->setFormula1(0);
                $validation->setFormula2(100);

                $validation = $sheet->getCell('G' . $i)
                    ->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
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
        
        // membuat file excel
        $writer = new Xlsx($spreadsheet);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'penilaian ngaji ' . $quranGrade->name . ' ' . $academicYear->year . ' ' . $academicYear->semester . '.xlsx';
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $writer->save($file_path);

        return response()->download($file_path)->deleteFileAfterSend(true);
    }
}
