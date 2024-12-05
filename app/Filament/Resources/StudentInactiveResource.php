<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentInactiveResource\Pages;
use App\Filament\Resources\StudentInactiveResource\RelationManagers;
use App\Models\Student;
use App\Models\StudentInactive;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentInactiveResource extends Resource
{
    protected static ?string $model = StudentInactive::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                CheckboxList::make('student_ids')
                    ->options(Student::whereDoesntHave('inactive')->get()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academic.year')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                TextColumn::make('academic.semester')
                    ->label('semester')
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageStudentInactives::route('/'),
        ];
    }
}
