<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtracurricularResource\Pages;
use App\Filament\Resources\ExtracurricularResource\RelationManagers;
use App\Filament\Resources\ExtracurricularResource\RelationManagers\StudentExtracurricularRelationManager;
use App\Filament\Resources\ExtracurricularResource\RelationManagers\TeacherExtracurricularRelationManager;
use App\Models\Extracurricular;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtracurricularResource extends Resource
{
    protected static ?string $model = Extracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function getNavigationLabel(): string
    {
        return __('extracurricular.extracurricular');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // buakan form
                Forms\Components\TextInput::make('name')
                    ->label(__('extracurricular.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_required')
                    ->label(__('extracurricular.is_required'))
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('extracurricular.name')),
                Tables\Columns\ToggleColumn::make('is_required')
                    ->label(__('extracurricular.is_required')), 
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
            StudentExtracurricularRelationManager::class,
            TeacherExtracurricularRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtracurriculars::route('/'),
            'create' => Pages\CreateExtracurricular::route('/create'),
            'edit' => Pages\EditExtracurricular::route('/{record}/edit'),
        ];
    }
}
