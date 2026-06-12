<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentCompetencyResource\Pages;
use App\Filament\Resources\StudentCompetencyResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Scopes\AcademicYearScope;
use App\Models\StudentCompetency;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentCompetencyResource extends Resource
{
    protected static ?string $model = StudentCompetency::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('teacher_subject_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('competency_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('metadata'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacherSubject.teacher.name')
                    ->label('Guru - Mata Pelajaran')
                    ->formatStateUsing(fn($record) => ($record->teacherSubject?->teacher?->name ?? '-') . ' - ' . ($record->teacherSubject?->subject?->name ?? '-'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('competency.description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')
                    ->options(AcademicYear::all()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'year' => $item->year . ' - ' . $item->semester,
                        ];
                    })->pluck('year', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('teacherSubject', fn (Builder $query) => $query->where('academic_year_id', $value))
                        );
                    }),
                SelectFilter::make('subject_id')
                    ->options(Subject::all()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('teacherSubject', fn (Builder $query) => $query->where('subject_id', $value))
                        );
                    }),
                SelectFilter::make('grade_id')
                    ->options(Grade::all()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('teacherSubject', fn (Builder $query) => $query->where('grade_id', $value))
                        );
                    }),
                SelectFilter::make('teacher_id')
                    ->options(Teacher::all()->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('teacherSubject', fn (Builder $query) => $query->where('teacher_id', $value))
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->withoutGlobalScope('App\Models\Scopes\AcademicYearScope');
            });
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
            'index' => Pages\ListStudentCompetencies::route('/'),
            'create' => Pages\CreateStudentCompetency::route('/create'),
            'edit' => Pages\EditStudentCompetency::route('/{record}/edit'),
            'upload' => Pages\UploadRdmtoStudentCompetencyPage::route('/upload'),
        ];
    }
}
