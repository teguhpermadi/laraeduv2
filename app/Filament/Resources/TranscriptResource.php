<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranscriptResource\Pages;
use App\Filament\Resources\TranscriptResource\RelationManagers;
use App\Models\Transcript;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TranscriptResource extends Resource
{
    protected static ?string $model = Transcript::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->sortable(),
                TextColumn::make('score')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
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
            ])
            ->groups([
                // 
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
            'index' => Pages\ListTranscripts::route('/'),
            'create' => Pages\CreateTranscript::route('/create'),
            'edit' => Pages\EditTranscript::route('/{record}/edit'),
        ];
    }
}
