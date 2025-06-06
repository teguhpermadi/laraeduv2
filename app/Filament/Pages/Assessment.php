<?php

namespace App\Filament\Pages;

use App\Enums\CurriculumEnum;
use App\Imports\StudentCompetencyImport;
use App\Models\Competency;
use App\Models\Grade;
use App\Models\StudentCompetency;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class Assessment extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.assessment';

    protected static ?string $slug = 'assessment/{id}';

    protected static bool $shouldRegisterNavigation = false;

    public TeacherSubject $record;

    public ?array $data = [];

    public $teacherSubject;
    public $competency_id;
    public $grade_id;
    public $subject_id;
    public $academic_year_id;
    public $teacher_subject_id;
    public $empty_state = [];
    public $visible = FALSE;

    public function mount($id): void
    {
        $data = TeacherSubject::find($id);

        if (!is_null($data)) {
            $this->form->fill([
                'competency_id' => ($data->competency->first()) ? $data->competency->first()->id : '',
                'grade_id' => $data['grade_id'],
                'subject_id' => $data['subject_id']
            ]);

            $this->teacherSubject = $data['id'];
            $this->academic_year_id = session()->get('academic_year_id');
        }

        $this->teacher_subject_id = $id;

        // cek student grade
        $students = TeacherSubject::with('studentGrade')->find($id)->studentGrade;

        if (count($students) == 0) {
            // jika student == 0
            $this->empty_state['heading'] = 'Anda tidak memiliki murid.';
            $this->empty_state['desc'] = 'Silahkan hubungi Admin!';
        } else {
            $this->empty_state['heading'] = '';
            $this->empty_state['desc'] = '';
        }

        // cek student competency
        $studentCompetency = StudentCompetency::query()
            ->where('teacher_subject_id', $this->teacherSubject)
            ->where('competency_id', $this->competency_id)
            ->get();
        if (count($students) != count($studentCompetency)) {
            $this->empty_state['heading'] = 'Skor Tidak Lengkap';
            $this->empty_state['desc'] = 'Lakukan reset skor pada kompetensi ini!';
        }

        // check curriculum
        $curriculum = $data->teacherGrade->curriculum;
        // ubah menjadi switch
        switch($curriculum){
            case CurriculumEnum::K13->value:
                $this->visible = false;
                break;

            default:
                $this->visible = false;
                break;
        }

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('assessment.identity'))
                    ->schema([
                        Grid::make([
                            'default' => 3,
                            'sm' => 1,
                            'md' => 2,
                        ])->schema([
                            Select::make('grade_id')
                                ->label(__('assessment.grade_id'))
                                ->options(Grade::pluck('name', 'id'))
                                ->disabled(),
                            Select::make('subject_id')
                                ->label(__('assessment.subject_id'))
                                ->options(Subject::pluck('name', 'id'))
                                ->disabled(),
                        ]),
                        Radio::make('competency_id')
                            ->label(__('assessment.competency_id'))
                            ->options(
                                function () {
                                    $comptencies = Competency::where('teacher_subject_id', $this->teacherSubject)
                                        ->get()
                                        ->pluck('description', 'id');
                                    return $comptencies;
                                }
                            )
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $competency = Competency::find($state); 

                                if($competency->code == 'TENGAH SEMESTER' || $competency->code == 'AKHIR SEMESTER' || $competency->teacherSubject->teacherGrade->curriculum == CurriculumEnum::KURMER->value){
                                    $this->visible = false;
                                } else {
                                    $this->visible = true;
                                }

                                $this->resetTable();

                            }),
                    ])
                    ->headerActions([
                        // add competency
                        Action::make('competency')
                            ->label(__('assessment.add_competency'))
                            ->form([
                                Section::make()
                                    ->columns(3)
                                    ->schema([
                                        Hidden::make('academic_year_id')
                                            ->default(session()->get('academic_year_id')),
                                        Hidden::make('teacher_subject_id')
                                            ->default($this->teacher_subject_id),
                                        TextInput::make('code')
                                            ->label(__('competency.code'))
                                            ->required(),
                                        TextInput::make('passing_grade')
                                            ->label(__('competency.passing_grade'))
                                            ->numeric()
                                            ->required(),

                                        // half semester
                                        Radio::make('half_semester')
                                            ->label(__('competency.half_semester'))
                                            ->default(false)
                                            ->boolean()
                                            ->required(),
                                    ]),
                                Textarea::make('description')
                                    ->label(__('competency.description'))
                                    ->required(),
                            ])
                            ->action(function (array $data): void {
                                // $record->author()->associate($data['authorId']);
                                // $record->save();
                                // dd($data);
                                Competency::create($data);
                            }),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StudentCompetency::query()
                    ->where('teacher_subject_id', $this->teacherSubject)
                    ->where('competency_id', $this->competency_id)
            )
            ->emptyStateHeading($this->empty_state['heading'])
            ->emptyStateDescription($this->empty_state['desc'])
            ->columns([
                // TextColumn::make('competency_id'),
                TextColumn::make('student.name')
                    ->label(__('assessment.student_id'))
                    ->searchable(),
                TextInputColumn::make('score')
                    ->label(__('assessment.score'))
                    ->rules(['numeric', 'min:0', 'max:100']),
                // score skill
                TextInputColumn::make('score_skill')
                    ->label(__('assessment.score_skill'))
                    ->visible($this->visible)
                    ->rules(['numeric', 'min:0', 'max:100']),
            ])
            ->bulkActions([
                BulkAction::make('score adjusment')
                    ->label(__('assessment.score_adjusment'))
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
                TableAction::make('reset')
                    ->icon('heroicon-s-arrow-path-rounded-square')
                    ->action(function () {
                        $this->resetStudentCompetency($this->teacher_subject_id);
                    })
                    ->button(),
                TableAction::make('download')
                    ->action(function () {
                        return $this->download();
                    })
                    ->button(),
                TableAction::make('upload')
                    ->slideOver()
                    ->closeModalByClickingAway(false)
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
                        $studentCompetencies = Excel::toArray(new StudentCompetencyImport, storage_path('/app/public/' . $data['file']));

                        $data = [];
                        foreach ($studentCompetencies as $row) {
                            foreach ($row as $value) {
                                StudentCompetency::where([
                                    'teacher_subject_id' => $value['teacher_subject_id'],
                                    'student_id' => $value['student_id'],
                                    'competency_id' => $value['competency_id'],
                                ])
                                    ->update([
                                        'score' => $value['score'],
                                        'score_skill' => $value['score_skill'],
                                    ]);
                            }
                        }
                    }),
                TableAction::make('leger')
                    ->color('success')
                    ->url(route('filament.admin.pages.leger.{id}', $this->teacher_subject_id)),
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
                'score_skill' => $key->score_skill,
            ]);
        }

        $originalScoreMin = (int) $original->min('score');
        $originalScoreMax = (int) $original->max('score');

        // Cek jika originalScoreMax dan originalScoreMin sama
        if ($originalScoreMax == $originalScoreMin) {
            // Tangani kasus ini, misalnya dengan mengatur nilai default
            $original->each(function ($item) use ($scoreMin, $scoreMax) {
                StudentCompetency::find($item['id'])
                    ->update([
                        'score' => $scoreMin, // atau nilai lain yang sesuai
                        'score_skill' => $scoreMin, // atau nilai lain yang sesuai
                    ]);
            });
            return;
        }

        // score adjusment
        $original->map(function ($item) use ($scoreMin, $scoreMax, $originalScoreMin, $originalScoreMax, $data) {
            $newScore = $scoreMin + (($item['score'] - $originalScoreMin) / ($originalScoreMax - $originalScoreMin) * ($scoreMax - $scoreMin));
            $newScoreSkill = $scoreMin + (($item['score_skill'] - $originalScoreMin) / ($originalScoreMax - $originalScoreMin) * ($scoreMax - $scoreMin));
            StudentCompetency::find($item['id'])
                ->update([
                    'score' => $newScore,
                    'score_skill' => $newScoreSkill,
                ]);
        });
    }

    public function resetStudentCompetency($teacher_subject_id)
    {
        // delete student competency
        StudentCompetency::where('teacher_subject_id', $teacher_subject_id)
            ->delete();

        // get students
        $students = TeacherSubject::with('studentGrade')
            ->find($teacher_subject_id)
            ->studentGrade->pluck('student_id');

        // get competency id from teacher subject
        $competencies = Competency::where('teacher_subject_id', $teacher_subject_id)->get()->pluck('id');

        // create new student competency
        // $data = [];
        foreach ($students as $student) {
            foreach ($competencies as $competency) {
                $data = [
                    'id' => Str::ulid()->toBase32(),
                    'teacher_subject_id' => $teacher_subject_id,
                    'student_id' => $student,
                    'competency_id' => $competency,
                    // 'created_at' => now(),
                ];
                
                // update or create
                StudentCompetency::updateOrCreate($data, ['score' => 0]);
            }
        }

        // notification
        Notification::make()
            ->title('Berhasil')
            ->body('Berhasil mereset nilai')
            ->success()
            ->send();
    }

    public function download()
    {
        $teacher_subject_id = $this->teacher_subject_id;

        $teacherSubject = TeacherSubject::with([
            'academic',
            'teacher',
            'grade.teacherGrade',
            'subject',
            'competency.studentCompetency.student',
            // 'exam',
        ])->find($teacher_subject_id);

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
            $spreadsheet->createSheet();
            // Membuat lembar pertama
            $sheet = $spreadsheet->getSheet($countSheet); // Indeks dimulai dari 0
            // $sheet->setTitle('Sheet' . ($countSheet + 1));
            $sheet->setTitle('Sheet ' . ($competency->code));

            $is_exam = false;

            if($competency->code == 'TENGAH SEMESTER' || $competency->code == 'AKHIR SEMESTER'){
                $is_exam = true;
            } else {
                $is_exam = false;
            }

            // identitas
            $identitas = [
                ['Identitas pelajaran'],
                [null],
                ['Nama Guru',': ' . $teacher->name],
                ['Mata Pelajaran', ': ' . $subject->name],
                ['Kelas', ': ' . $grade->name],
                ['Tahun Akademik', ': ' . $academic->year],
                ['Semester', ': ' . $academic->semester],
                
            ];

            // jika bukan ujian
            if($is_exam == false){
                $identitas[9] = ['Kompetensi', ': (' . $competency->code . ') ', $competency->description];
                $identitas[10] = ['Keterampilan', ': (' . $competency->code_skill . ') ', $competency->description_skill];
            }

            // dd($identitas);

            $sheet->fromArray($identitas);

            // kosongkan datanya
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

            // dd($data);

            $sheet->fromArray($data, null, 'A13', true);

            $countSheet++;

            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);

            // count student
            $rowStudent = 13 + $countStudent;

            // bisa di edit
            $sheet->getStyle('C14:C' . $rowStudent)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            
            // jika k13
            if ($teacherSubject->teacherGrade->curriculum === CurriculumEnum::K13->value) {
                $sheet->getStyle('D14:D' . $rowStudent)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            }

            // jika ujian
            if($is_exam == true){
                // proteksi cell D
                $sheet->getStyle('D13:D' . $rowStudent)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
                // warna font cell D putih
                $sheet->getStyle('D13:D' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');
            }

            // proteksi semua cell
            $sheet->getProtection()->setPassword('PhpSpreadsheet');
            $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

            // validasi tiap-tiap cell
            for ($i = 14; $i <= $rowStudent; $i++) {
                $validation = $sheet->getCell('C' . $i)->getDataValidation();
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

                // jika k13
                if ($teacherSubject->teacherGrade->curriculum === CurriculumEnum::K13->value || $is_exam == true) {
                    $validation = $sheet->getCell('D' . $i)->getDataValidation();
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

            // berikan warna putih pada font pada cell D jika bukan k13
            if ($teacherSubject->teacherGrade->curriculum !== CurriculumEnum::K13->value) {
                $sheet->getStyle('D13:D' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');
            }

            // berikan warna putih pada text pada cell E
            $sheet->getStyle('E13:E' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');

            // berikan warna putih pada text pada cell F
            $sheet->getStyle('F13:F' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');

            // berikan warna putih pada text pada cell G
            $sheet->getStyle('G13:G' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');

            // Set active cell to C14
            $sheet->setSelectedCell('C14');
        }

        // Membuat file Excel
        $writer = new Xlsx($spreadsheet);

        // Set sheet pertama sebagai sheet aktif
        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx'); // <<< HERE
        // $filename = "studentCompetency-".$subject->code.".xlsx"; // <<< HERE
        $filename = "nilai " . $teacherSubject->subject->name . ' ' . $teacherSubject->grade->name . ".xlsx"; // <<< HERE
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $writer->save($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }
}
