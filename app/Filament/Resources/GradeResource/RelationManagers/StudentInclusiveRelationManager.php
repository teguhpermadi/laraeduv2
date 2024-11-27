<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use App\Models\Student;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentInclusiveRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;

    protected static string $relationship = 'StudentInclusive';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('student_id')
                    ->label('Nama Siswa')
                    ->options(
                        // dapatkan semua siswa dalam grade ini
                        Student::whereHas('studentGrade', function (Builder $query) {
                            $query->where('grade_id', $this->ownerRecord->id);
                        })->whereDoesntHave('studentInclusive')->pluck('name', 'id')
                    )
                    ->required(),
                Select::make('teacher_id')
                    ->label('Guru Pembimbing')
                    ->options(Teacher::all()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.name'),
                Tables\Columns\TextColumn::make('teacher.name'),
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
