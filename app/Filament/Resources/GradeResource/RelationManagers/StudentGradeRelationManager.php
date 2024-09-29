<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use App\Models\Student;
use App\Models\StudentGrade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentGradeRelationManager extends RelationManager
{
    protected static string $relationship = 'StudentGrade';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Select::make('student_ids')
                    ->multiple()
                    ->searchable()
                    ->options(Student::whereDoesntHave('studentGrade')->get()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('studentGrade')
            ->columns([
                Tables\Columns\TextColumn::make('student.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->using(function($data){
                        $data += ['grade_id' => $this->getOwnerRecord()->getKey()];
                        
                        $insert = [];
                        foreach ($data['student_ids'] as $student) {
                            $newData = [
                                'academic_year_id' => $data['academic_year_id'],
                                'grade_id' => $data['grade_id'],
                                'student_id' => $student
                            ];

                            $insert[] = StudentGrade::create($newData);
                        }

                        return $insert[0];
                    }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
