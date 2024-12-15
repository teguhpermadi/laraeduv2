<?php

namespace App\Filament\Resources;

use App\Enums\CurriculumEnum;
use App\Filament\Resources\CompetencyResource\Pages;
use App\Filament\Resources\CompetencyResource\RelationManagers;
use App\Models\Competency;
use App\Models\Grade;
use App\Models\TeacherSubject;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetencyResource extends Resource
{
    protected static ?string $model = Competency::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pelajaran Ku';

    protected static ?int $navigationSort = 7;


    public static function getNavigationLabel(): string
    {
        return __('competency.competency');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Fieldset::make('identity')
                    ->label(__('competency.identity'))
                    ->schema([
                        Select::make('grade_id')
                            ->label(__('competency.grade_id'))
                            ->options(function (callable $get, callable $set) {
                                $data = TeacherSubject::myGrade()->get();

                                // function map
                                $data = $data->mapWithKeys(function ($item) {
                                    return [$item->grade->id => $item->grade->name . ' ' . $item->grade->phase . ' ' . ($item->grade->is_inclusive ? '(Inklusif)' : '')];
                                });

                                return $data;
                            })->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('subject_id', null);
                                $set('teacher_subject_id', null);
                            })
                            ->reactive()
                            ->required(),
                        Select::make('subject_id')
                            ->label(__('competency.subject_id'))
                            ->options(function (callable $get, callable $set) {
                                if ($get('grade_id')) {
                                    $data = TeacherSubject::mySubjectByGrade($get('grade_id'))->get()->pluck('subject.code', 'subject.id');

                                    return $data;
                                }
                                return [];
                            })->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $data = TeacherSubject::where('grade_id', $get('grade_id'))
                                    ->where('teacher_id', auth()->user()->userable->userable_id)
                                    ->where('subject_id', $get('subject_id'))->first();
                                if ($data) {
                                    $set('teacher_subject_id', $data->id);
                                } else {
                                    $set('teacher_subject_id', null);
                                }
                            })
                            ->reactive()
                            ->required(),

                        TextInput::make('passing_grade')
                            ->label(__('competency.passing_grade'))
                            ->numeric()
                            ->required(),

                    ])
                    ->columns(3),

                Hidden::make('teacher_subject_id')
                    ->required(),
                Fieldset::make('competency')
                    ->label(__('competency.competency'))
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('referensi')
                                ->slideOver()
                                ->closeModalByClickingAway(false)
                                ->modalContent(function (callable $get) {
                                    $teacherSubject = null;

                                    if ($get('grade_id') && $get('subject_id')) {
                                        // dapatkan grade dari grade_id
                                        $grade = Grade::find($get('grade_id'));
                                        $level = $grade->grade;

                                        // dapatkan semua grade dengan level yang sama
                                        $grades = Grade::where('grade', $level)->get();
                                        // dapatkan grade_id dari grade yang sama
                                        $grade_ids = $grades->pluck('id');

                                        // cari referensi competency berdasarkan subject_id dan grade_id
                                        $teacherSubject = TeacherSubject::where('subject_id', $get('subject_id'))
                                            ->whereIn('grade_id', $grade_ids)
                                            ->where('academic_year_id', session()->get('academic_year_id'))
                                            ->with('competency')
                                            ->first()->id;
                                    }

                                    // tampilkan view competency-reference
                                    return view('competency-reference', [
                                        'teacherSubjects' => $teacherSubject,
                                    ]);
                                })
                                ->modalSubmitAction(false)
                        ])
                        ->columnSpanFull()
                        ->visible(function (callable $get) {
                            // jika grade id memiliki is_inclusive true maka tampilkan
                            $grade = Grade::find($get('grade_id'));
                            if ($grade) {
                                return $grade->is_inclusive;
                            }

                            return false;
                        }),
                        TextInput::make('code')
                            ->label(__('competency.code'))
                            ->helperText('Kode kompetensi harus unik dan tidak boleh sama dengan kompetensi lain yang Anda miliki')
                            ->required(),
                        Textarea::make('description')
                            ->label(__('competency.description'))
                            ->required(),

                        // skill
                        TextInput::make('code_skill')
                            ->visible(function (callable $get) {
                                if ($get('teacher_subject_id')) {
                                    $teacherSubject = TeacherSubject::with('teacherGrade')->find($get('teacher_subject_id'));
                                    if ($teacherSubject->teacherGrade->curriculum == CurriculumEnum::K13->value) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                } else {
                                    return false;
                                }
                            })
                            ->required()
                            ->label(__('competency.code_skill')),
                        Textarea::make('description_skill')
                            ->visible(function (callable $get) {
                                if ($get('teacher_subject_id')) {
                                    $teacherSubject = TeacherSubject::with('teacherGrade')->find($get('teacher_subject_id'));
                                    if ($teacherSubject->teacherGrade->curriculum == CurriculumEnum::K13->value) {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                } else {
                                    return false;
                                }
                            })
                            ->label(__('competency.description_skill'))
                            ->required(),

                    ])
                    ->columns(2),

                // half semester
                Radio::make('half_semester')
                    ->label(__('competency.half_semester'))
                    ->default(false)
                    ->boolean()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('competency.code')),
                TextColumn::make('description')
                    ->wrap()
                    ->label(__('competency.description')),
                ToggleColumn::make('half_semester')
                    ->label(__('competency.half_semester')),
                TextInputColumn::make('passing_grade')
                    ->label(__('competency.passing_grade')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // make bulk action to update passing grade
                    Tables\Actions\BulkAction::make('updatePassingGrade')
                        ->label('Update Passing Grade')
                        ->form([
                            TextInput::make('passing_grade')
                                ->label(__('competency.passing_grade'))
                                ->numeric()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['passing_grade' => $data['passing_grade']]);
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompetencies::route('/'),
            'create' => Pages\CreateCompetency::route('/create'),
            'edit' => Pages\EditCompetency::route('/{record}/edit'),
        ];
    }
}
