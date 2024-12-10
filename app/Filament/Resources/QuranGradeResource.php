<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuranGradeResource\Pages;
use App\Filament\Resources\QuranGradeResource\RelationManagers;
use App\Filament\Resources\QuranGradeResource\RelationManagers\StudentQuranGradeRelationManager;
use App\Filament\Resources\QuranGradeResource\RelationManagers\TeacherQuranGradeRelationManager;
use App\Models\QuranGrade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuranGradeResource extends Resource
{
    protected static ?string $model = QuranGrade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mengaji';

    protected static ?string $modelLabel = 'Kelas Mengaji';

    protected static ?string $pluralModelLabel = 'Kelas Mengaji';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('quran-grade.fields.name.label'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('level')
                    ->label(__('quran-grade.fields.level.label'))
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('quran-grade.fields.name.label')),
                Tables\Columns\TextColumn::make('level')
                    ->label(__('quran-grade.fields.level.label')),
                Tables\Columns\TextColumn::make('teacherQuranGrade.teacher.name')
                    ->label(__('quran-grade.fields.teacher.label')),
                Tables\Columns\TextColumn::make('student_quran_grade_count')
                    ->label(__('quran-grade.fields.students.label'))
                    ->counts('studentQuranGrade')
                    ->suffix(' siswa'),
            ])
            ->filters([
                //
            ])
            ->actions([
                RelationManagerAction::make('student-quran-grade-relation-manager')
                    ->label('Siswa')
                    ->button()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->relationManager(StudentQuranGradeRelationManager::make()),
                RelationManagerAction::make('teacher-quran-grade-relation-manager')
                    ->label('Guru')
                    ->button()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->relationManager(TeacherQuranGradeRelationManager::make()),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            TeacherQuranGradeRelationManager::class,
            StudentQuranGradeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuranGrades::route('/'),
            'create' => Pages\CreateQuranGrade::route('/create'),
            'edit' => Pages\EditQuranGrade::route('/{record}/edit'),
        ];
    }
}
