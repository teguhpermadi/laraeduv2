<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\Extracurricular;
use App\Models\TeacherExtracurricular;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                Select::make('extracurricular_id')
                    ->label(__('teacher.relation.teacher_extracurricular.extracurricular'))
                    ->options(Extracurricular::pluck('name', 'id'))
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
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->using(function (array $data):Model {
                        return TeacherExtracurricular::updateOrCreate(
                            ['academic_year_id' => session('academic_year_id'), 'teacher_id' => $this->ownerRecord->id, 'extracurricular_id' => $data['extracurricular_id']],
                            ['academic_year_id' => session('academic_year_id'), 'teacher_id' => $this->ownerRecord->id, 'extracurricular_id' => $data['extracurricular_id']]
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
