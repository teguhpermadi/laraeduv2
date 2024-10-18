<?php

namespace App\Filament\Pages;

use App\Jobs\ResetStudentCompetency;
use App\Models\Competency;
use App\Models\Grade;
use App\Models\StudentCompetency;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
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
                            ->afterStateUpdated(function () {
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
                    ->action(function(){
                        $this->resetStudentCompetency($this->teacher_subject_id, $this->competency_id);
                    })
                    ->button(),
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
            StudentCompetency::find($item['id'])
                ->update([
                    'score' => $newScore,
                ]);
        });
    }

    public function resetStudentCompetency($teacher_subject_id, $competency_id)
    {
        // get students
        $students = TeacherSubject::with('studentGrade')
                        ->find($teacher_subject_id)
                        ->studentGrade->pluck('student_id');

        // delete student competency
        StudentCompetency::where('teacher_subject_id', $teacher_subject_id)
            ->where('competency_id', $competency_id)
            ->delete();
        
        // create new student competency
        $data = [];
        foreach ($students as $student) {
            $data[] = [
                'teacher_subject_id' => $teacher_subject_id,
                'student_id' => $student,
                'competency_id' => $competency_id,
                'created_at' => now(),
            ];
        }

        StudentCompetency::insert($data);
    }
}
