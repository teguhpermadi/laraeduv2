<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

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
                Forms\Components\Select::make('subject_id')
                    ->label(__('teacher.relation.subjects.subject'))
                    ->options(Subject::pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('grade_id')
                    ->label(__('teacher.relation.subjects.grade'))
                    ->options(Grade::pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('time_allocation')
                    ->label(__('teacher.relation.subjects.time_allocation'))
                    ->numeric()
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
                Tables\Columns\TextColumn::make('time_allocation')
                    ->label(__('teacher.relation.subjects.time_allocation')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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