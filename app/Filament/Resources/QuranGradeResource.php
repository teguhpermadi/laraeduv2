<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuranGradeResource\Pages;
use App\Filament\Resources\QuranGradeResource\RelationManagers;
use App\Models\QuranGrade;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuranGradeResource extends Resource
{
    protected static ?string $model = QuranGrade::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'Kelas Quran';
    
    protected static ?string $pluralModelLabel = 'Kelas-kelas Quran';

    // label navigation berdasarkan lang
    public static function getNavigationLabel(): string
    {
        return __('quran-grade.resource.label');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('quran-grade.fields.name.label'))
                    ->placeholder(__('quran-grade.fields.name.placeholder')),
                TextInput::make('level')
                    ->label(__('quran-grade.fields.level.label'))
                    ->placeholder(__('quran-grade.fields.level.placeholder'))
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('quran-grade.fields.name.label')),
                TextColumn::make('level')
                    ->label(__('quran-grade.fields.level.label')),
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
            'index' => Pages\ManageQuranGrades::route('/'),
        ];
    }
}
