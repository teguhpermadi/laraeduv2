<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Model;

class SubjectsRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;
    
    protected static string $relationship = 'subject';

    protected static ?string $title = 'Mata Pelajaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('subject_ids')
                    ->label(__('teacher.relation.subjects.subject'))
                    ->options(
                        // tampilkan semua subject yang belum dimiliki oleh guru ini
                        Subject::whereDoesntHave('teacherSubject', function ($query) {
                            $query->where('teacher_id', $this->getOwnerRecord()->getKey());
                        })->pluck('name', 'id')
                    )
                    ->multiple()
                    ->required(),
                Select::make('grade_id')
                    ->label(__('teacher.relation.subjects.grade'))
                    ->options(
                        Grade::all()->mapWithKeys(function ($grade) {
                            return [$grade->id => $grade->name . ($grade->is_inclusive ? ' (inklusif)' : '')];
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
                ->using(function (array $data, string $model): Model {
                    // return $model::create($data);
                    // setiap subject yang dipilih, tambahkan ke teacherSubject
                    foreach ($data['subject_ids'] as $subject_id) {
                        $teacherSubject = new TeacherSubject();
                        $teacherSubject->academic_year_id = $data['academic_year_id'];
                        $teacherSubject->teacher_id = $this->getOwnerRecord()->getKey();
                        $teacherSubject->subject_id = $subject_id;
                        $teacherSubject->grade_id = $data['grade_id'];
                        $teacherSubject->time_allocation = $data['time_allocation'];
                        $teacherSubject->save();
                    }
                    return $teacherSubject;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 