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
                Repeater::make('extracurricular')
                    ->schema([
                        Hidden::make('academic_year_id')
                            ->default(session('academic_year_id')),
                        Select::make('extracurricular_id')
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
                    ])
                    ->columnSpanFull()
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
                    ->using(function (array $data, string $model): Model {
                        // dd($data);
                        foreach ($data['extracurricular'] as $extracurricular) {
                            $teacherExtracurricular = new TeacherExtracurricular();
                            $teacherExtracurricular->academic_year_id = $extracurricular['academic_year_id'];
                            $teacherExtracurricular->teacher_id = $this->getOwnerRecord()->getKey();
                            $teacherExtracurricular->extracurricular_id = $extracurricular['extracurricular_id'];
                            $teacherExtracurricular->save();
                        }
                        
                        return $teacherExtracurricular;
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
