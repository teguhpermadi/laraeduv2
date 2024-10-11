<?php

namespace App\Filament\Pages;

use App\Models\Competency;
use App\Models\Grade;
use App\Models\StudentCompetency;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
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

    public function mount($id): void
    {
        $data = TeacherSubject::find($id);

        if (!is_null($data)) {
            $this->form->fill([
                'competency_id' => $data->competency->first()->id,
                'grade_id' => $data['grade_id'],
                'subject_id' => $data['subject_id']
            ]);

            $this->teacherSubject = $data['id'];
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
                                        ->pluck('id', 'id');
                                    return $comptencies;
                                }
                            )
                            ->live()
                            ->afterStateUpdated(function () {
                                $this->resetTable();
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
}
