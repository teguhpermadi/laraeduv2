<?php

namespace App\Filament\Resources\SubjectResource\RelationManagers;

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class TeacherSubjectRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;
    
    protected static string $relationship = 'teacherSubject';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Select::make('grade_id')
                    ->label(__('subject.grade.name'))
                    ->options(Grade::pluck('name', 'id'))
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) { 
                        $subject_id = $this->getOwnerRecord()->getKey();
                        
                        return $rule
                                ->where('academic_year_id', $get('academic_year_id'))
                                ->where('subject_id', $subject_id)
                                ->where('grade_id', $get('grade_id'));
                        }, ignoreRecord: true)
                    ->required(),
                Select::make('teacher_id')
                    ->label(__('subject.teacher.name'))
                    ->options(Teacher::pluck('name', 'id'))
                    ->required(),
                TextInput::make('time_allocation')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('grade_id')
            ->columns([
                Tables\Columns\TextColumn::make('grade.name')
                    ->label(__('subject.grade.name')),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('subject.teacher.name')),
                Tables\Columns\TextColumn::make('time_allocation')
                    ->label(__('subject.time_allocation')),
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
