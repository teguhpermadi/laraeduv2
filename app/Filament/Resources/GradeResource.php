<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Filament\Resources\GradeResource\RelationManagers;
use App\Filament\Resources\GradeResource\RelationManagers\StudentGradeRelationManager;
use App\Filament\Resources\GradeResource\RelationManagers\TeacherGradeRelationManager;
use App\Models\Grade;
use App\Enums\PhaseEnum;
use App\Filament\Exports\GradeExporter;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function getModelLabel(): string
    {
        return __('grade.grade');
    }

    protected static ?int $navigationSort = 4;


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
                ToggleButtons::make('is_inclusive')
                    ->boolean()
                    ->default(false)
                    ->required()
                    ->inline()
                    ->label(__('grade.is_inclusive')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('grade.name'))
                    ->sortable(),
                TextColumn::make('grade')
                    ->label(__('grade.grade.name'))
                    ->sortable(),
                TextColumn::make('phase')
                    ->label(__('grade.phase'))
                    ->sortable(),
                TextColumn::make('teacherGrade.teacher.name')
                    ->label(__('grade.teacherGrade.teacher.name'))
                    ->sortable(),
                TextColumn::make('student_grade_count')
                    ->label(__('grade.student_grade_count'))
                    ->counts('studentGrade')
                    ->suffix(' siswa')
                    ->sortable(),
                IconColumn::make('is_inclusive')
                    ->label(__('grade.is_inclusive'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                RelationManagerAction::make('student-grade-relation-manager')
                    ->label('siswa')
                    ->slideOver()
                    ->button()
                    ->relationManager(StudentGradeRelationManager::class),
                RelationManagerAction::make('teacher-grade-relation-manager')
                    ->label('walikelas')
                    ->slideOver()
                    ->button()
                    ->relationManager(TeacherGradeRelationManager::class),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->exporter(GradeExporter::class),
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
