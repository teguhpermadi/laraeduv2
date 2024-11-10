<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuranGradeResource\Pages;
use App\Filament\Resources\QuranGradeResource\RelationManagers;
use App\Models\QuranGrade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuranGradeResource extends Resource
{
    protected static ?string $model = QuranGrade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Mengaji';

    protected static ?string $modelLabel = 'Kelas Mengaji';
    
    protected static ?string $pluralModelLabel = 'Kelas Mengaji';

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
                Tables\Columns\TextColumn::make('teacherQuranGrades.teacher.name')
                    ->label(__('quran-grade.fields.teacher.label')),
                Tables\Columns\TextColumn::make('studentQuranGrades.count') 
                    ->counts('studentQuranGrades')
                    ->label(__('quran-grade.fields.students.label')),
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
            'index' => Pages\ListQuranGrades::route('/'),
            'create' => Pages\CreateQuranGrade::route('/create'),
            'edit' => Pages\EditQuranGrade::route('/{record}/edit'),
        ];
    }
}
