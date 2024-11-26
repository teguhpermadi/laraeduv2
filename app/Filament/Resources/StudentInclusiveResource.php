<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentInclusiveResource\Pages;
use App\Filament\Resources\StudentInclusiveResource\RelationManagers;
use App\Models\Student;
use App\Models\StudentInclusive;
use App\Models\Teacher;
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

class StudentInclusiveResource extends Resource
{
    protected static ?string $model = StudentInclusive::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Inklusif';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('student_id')
                    ->label('Nama Siswa')
                    ->options(
                        // semua siswa yang tidak memiliki student inclusive
                        Student::whereDoesntHave('studentInclusive')->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
                Select::make('teacher_id')
                    ->label('Guru Pembimbing')
                    ->options(
                        Teacher::all()->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name'),
                TextColumn::make('student.studentGradeFirst.grade.name'),
                TextColumn::make('teacher.name'),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStudentInclusives::route('/'),
        ];
    }
}
