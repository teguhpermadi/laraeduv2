<?php

namespace App\Filament\Resources;

use App\Enums\CurriculumEnum;
use App\Filament\Resources\CompetencyResource\Pages;
use App\Filament\Resources\CompetencyResource\RelationManagers;
use App\Models\Competency;
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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetencyResource extends Resource
{
    protected static ?string $model = Competency::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pelajaran Ku';

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
                                $data = TeacherSubject::myGrade()->get()->pluck('grade.name', 'grade.id');
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
                        TextInput::make('code')
                            ->label(__('competency.code'))
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
                    ->label(__('competency.description')),
                ToggleColumn::make('half_semester')
                    ->label(__('competency.half_semester'))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
