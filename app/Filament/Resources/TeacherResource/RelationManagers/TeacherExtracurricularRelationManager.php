<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\Extracurricular;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherExtracurricularRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;
    
    protected static string $relationship = 'teacherExtracurricular';

    protected static ?string $title = 'Guru Extrakurikuler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Forms\Components\Select::make('extracurricular_id')
                    ->label(__('teacher.relation.teacher_extracurricular.extracurricular'))
                    ->options(function () {
                        // tampilkan semua extracurricular yang belum ada di teacher_extracurricular pada academic_year_id yang sama
                        return Extracurricular::whereNotIn('id', function ($query) {
                            $query->select('extracurricular_id')
                                ->from('teacher_extracurriculars')
                                ->where('academic_year_id', session('academic_year_id'));
                        })->pluck('name', 'id');
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.year')
                    ->label(__('teacher.relation.teacher_extracurricular.academic_year')),
                Tables\Columns\TextColumn::make('academicYear.semester')
                    ->label(__('teacher.relation.teacher_extracurricular.semester')),
                Tables\Columns\TextColumn::make('extracurricular.name')
                    ->label(__('teacher.relation.teacher_extracurricular.extracurricular')),
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
            ]);
    }
}
