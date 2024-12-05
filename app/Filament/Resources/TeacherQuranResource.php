<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherQuranResource\Pages;
use App\Filament\Resources\TeacherQuranResource\RelationManagers;
use App\Models\Teacher;
use App\Models\TeacherQuran;
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

class TeacherQuranResource extends Resource
{
    protected static ?string $model = TeacherQuran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // navigation group
    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 7;

    // navigation label
    protected static ?string $navigationLabel = 'Guru Quran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                CheckboxList::make('teacher_ids')
                    ->label('Guru')
                    ->options(Teacher::all()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicYear.year')
                    ->label('Tahun Ajaran'),
                TextColumn::make('academicYear.semester')
                    ->label('Semester'),
                TextColumn::make('teacher.name')
                    ->label('Guru'),
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
            'index' => Pages\ManageTeacherQurans::route('/'),
        ];
    }
}
