<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Enums\CurriculumEnum;
use App\Models\Grade;
use App\Models\TeacherGrade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

class TeacherGradesRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;
    
    protected static string $relationship = 'teacherGrade';
    
    protected static ?string $title = 'Guru Kelas';

    protected static ?string $recordTitleAttribute = 'grade';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('grade_id')
                    ->label(__('teacherGrade.grade_id'))
                    ->options(Grade::pluck('name', 'id'))
                    ->required(),
                Select::make('curriculum')
                    ->label(__('teacherGrade.curriculum'))
                    ->options(CurriculumEnum::class)
                    ->required(),
                Hidden::make('academic_year_id')    
                    ->default(session('academic_year_id')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grade.name')
                    ->label(__('teacherGrade.grade_id')),
                Tables\Columns\TextColumn::make('curriculum')
                    ->label(__('teacherGrade.curriculum')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver(),
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