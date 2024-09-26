<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Filament\Resources\AcademicYearResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function getNavigationLabel(): string
    {
        return __('academic-year.list.academic_year');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('year')
                    ->mask('9999/9999')
                    ->label(__('academic-year.create.year'))
                    ->required(),
                Select::make('semester')
                    ->label(__('academic-year.create.semester'))
                    ->options(['ganjil' => 'ganjil', 'genap' => 'genap'])
                    ->required(),
                Select::make('teacher_id')
                    ->label(__('academic-year.create.teacher_id'))
                    ->options(Teacher::all()->pluck('name', 'id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->label(__('academic-year.list.year')),
                TextColumn::make('semester')
                    ->label(__('academic-year.list.semester')),
                TextColumn::make('teacher.name')
                    ->label(__('academic-year.list.teacher_id')),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
