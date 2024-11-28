<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class SubjectsRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;

    protected static string $relationship = 'subject';

    protected static ?string $title = 'Mata Pelajaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('subjects')
                    ->schema([
                        Section::make()
                            ->schema([
                                Hidden::make('academic_year_id')
                                    ->default(session('academic_year_id')),
                                Select::make('subject_id')
                                    ->label(__('teacher.relation.subjects.subject'))
                                    ->options(Subject::get()->pluck('name', 'id'))
                                    ->required()
                                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                                        return $rule->where('academic_year_id', $get('academic_year_id'))
                                            ->where('teacher_id', $get('teacher_id'))
                                            ->where('subject_id', $get('subject_id'))
                                            ->where('grade_id', $get('grade_id'));
                                    }),
                                Select::make('grade_id')
                                    ->label(__('teacher.relation.subjects.grade'))
                                    ->options(
                                        Grade::all()->mapWithKeys(function ($grade) {
                                            return [$grade->id => $grade->name . ($grade->is_inclusive ? ' (inklusif)' : '')];
                                        })
                                    )
                                    ->required()
                                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                                        return $rule->where('academic_year_id', $get('academic_year_id'))
                                            ->where('teacher_id', $get('teacher_id'))
                                            ->where('subject_id', $get('subject_id'))
                                            ->where('grade_id', $get('grade_id'));
                                    }),
                                TextInput::make('time_allocation')
                                    ->label(__('teacher.relation.subjects.time_allocation'))
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('teacher.relation.subjects.subject')),
                Tables\Columns\TextColumn::make('grade.name')
                    ->label(__('teacher.relation.subjects.grade')),
                IconColumn::make('grade.is_inclusive')
                    ->label(__('grade.is_inclusive'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('time_allocation')
                    ->label(__('teacher.relation.subjects.time_allocation')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->using(function (array $data, string $model): Model {
                        // dd($data);
                        // return $model::create($data);
                        foreach ($data['subjects'] as $subject) {
                            $teacherSubject = new TeacherSubject();
                            $teacherSubject->academic_year_id = $subject['academic_year_id'];
                            $teacherSubject->teacher_id = $this->getOwnerRecord()->getKey();
                            $teacherSubject->subject_id = $subject['subject_id'];
                            $teacherSubject->grade_id = $subject['grade_id'];
                            $teacherSubject->time_allocation = $subject['time_allocation'];
                            $teacherSubject->save();
                        }

                        return $teacherSubject;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
