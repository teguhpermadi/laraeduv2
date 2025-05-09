<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Grade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentGradeRelationManager extends RelationManager
{
    protected static string $relationship = 'studentGrade';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('grade_id')
                    ->label('Grade')
                    ->options(Grade::all()->pluck('name', 'id')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('grade.name')
            ->columns([
                TextColumn::make('academic.year'),
                TextColumn::make('academic.semester'),
                TextColumn::make('grade.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->closeModalByClickingAway(false)
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->closeModalByClickingAway(false)
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
