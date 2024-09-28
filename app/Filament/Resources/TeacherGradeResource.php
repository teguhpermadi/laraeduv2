<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherGradeResource\Pages;
use App\Filament\Resources\TeacherGradeResource\RelationManagers;
use App\Models\Grade;
use App\Models\Teacher;
use App\Models\TeacherGrade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherGradeResource extends Resource
{
    protected static ?string $model = TeacherGrade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('teacherGrade.teachergrade');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')->default(session()->get('academic_year_id')),
                Select::make('teacher_id')
                    ->label(__('teacherGrade.teacher_id'))
                    ->options(Teacher::pluck('name', 'id'))
                    ->required(),
                Select::make('grade_id')
                    ->label(__('teacherGrade.grade_id'))
                    ->options(Grade::pluck('name', 'id'))
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grade.name')
                    ->label(__('teacherGrade.grade_id')),
                TextColumn::make('teacher.name')
                    ->label(__('teacherGrade.teacher_id')),
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
            'index' => Pages\ListTeacherGrades::route('/'),
            'create' => Pages\CreateTeacherGrade::route('/create'),
            'edit' => Pages\EditTeacherGrade::route('/{record}/edit'),
        ];
    }
}
