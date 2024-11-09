<?php

namespace App\Filament\Resources\ExtracurricularResource\RelationManagers;

use App\Models\Teacher;
use App\Models\TeacherExtracurricular;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherExtracurricularRelationManager extends RelationManager
{
    protected static string $relationship = 'teacherExtracurricular';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Forms\Components\Select::make('teacher_id')
                    ->label(__('extracurricular.teacher'))
                    ->options(Teacher::all()->pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->label(__('extracurricular.academic_year')),
                Tables\Columns\TextColumn::make('academicYear.semester')
                    ->label(__('extracurricular.semester')),
                Tables\Columns\TextColumn::make('extracurricular.name')
                    ->label(__('extracurricular.extracurricular')),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('extracurricular.teacher')),
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
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // tampilkan teacher_extracurricular yang hanya pada academic_year_id yang sama
                return $query->where('academic_year_id', session('academic_year_id'));
            });
    }
}
