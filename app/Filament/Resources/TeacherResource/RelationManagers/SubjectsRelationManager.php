<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subject';

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $title = 'Mata Pelajaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('subject_id')
                    ->label(__('teacher.relation.subjects.subject'))
                    ->options(Subject::get()->pluck('name', 'id'))
                    ->required(),
                Select::make('grade_id')
                    ->label(__('teacher.relation.subjects.grade'))
                    ->options(
                        Grade::all()->mapWithKeys(function ($grade) {
                            return [$grade->id => $grade->name.($grade->is_inclusive ? ' (inklusif)' : '')];
                        })
                    )
                    ->required(),
                TextInput::make('time_allocation')
                    ->label(__('teacher.relation.subjects.time_allocation'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable()
                    ->label(__('teacher.relation.subjects.subject')),
                Tables\Columns\TextColumn::make('grade.name')
                    ->sortable()
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
                Action::make('addSubjects')
                    ->label('Tambah Mata Pelajaran')
                    ->modalHeading('Tambah Mata Pelajaran')
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->form([
                        Select::make('subject_id')
                            ->label(__('teacher.relation.subjects.subject'))
                            ->options(Subject::get()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        CheckboxList::make('grade_ids')
                            ->label(__('teacher.relation.subjects.grade'))
                            ->options(
                                Grade::all()->mapWithKeys(function ($grade) {
                                    return [$grade->id => $grade->name.($grade->is_inclusive ? ' (inklusif)' : '')];
                                })
                            )
                            ->columns(3)
                            ->required()
                            ->bulkToggleable(),
                        TextInput::make('time_allocation')
                            ->label(__('teacher.relation.subjects.time_allocation'))
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        foreach ($data['grade_ids'] as $grade_id) {
                            TeacherSubject::firstOrCreate(
                                [
                                    'academic_year_id' => session('academic_year_id'),
                                    'teacher_id' => $this->ownerRecord->id,
                                    'subject_id' => $data['subject_id'],
                                    'grade_id' => $grade_id,
                                ],
                                [
                                    'time_allocation' => $data['time_allocation'],
                                ]
                            );
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
