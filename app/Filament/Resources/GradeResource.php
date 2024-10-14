<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Filament\Resources\GradeResource\RelationManagers;
use App\Filament\Resources\GradeResource\RelationManagers\StudentGradeRelationManager;
use App\Filament\Resources\GradeResource\RelationManagers\TeacherGradeRelationManager;
use App\Models\Grade;
use App\PhaseEnum;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('grade.grade');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('grade.name'))
                    ->required(),
                TextInput::make('grade')
                    ->label(__('grade.grade.name'))
                    ->numeric()
                    ->required(),
                Select::make('phase')
                    ->label(__('grade.phase'))
                    ->options(PhaseEnum::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('grade.name')),
                TextColumn::make('grade')
                    ->label(__('grade.grade.name')),
                TextColumn::make('phase')
                    ->label(__('grade.phase')),
                TextColumn::make('teacherGrade.teacher.name')
                    ->label(__('grade.teacherGrade.teacher.name')),
                TextColumn::make('student_grade_count')
                    ->label(__('grade.student_grade_count'))
                    ->counts('studentGrade')
                    ->suffix(' siswa'),
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
            TeacherGradeRelationManager::class,
            StudentGradeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
