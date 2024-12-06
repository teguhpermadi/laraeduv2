<?php

namespace App\Filament\Resources\ExtracurricularResource\RelationManagers;

use App\Enums\LinkertScaleEnum;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentGrade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\CreateAction;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class StudentExtracurricularRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;

    protected static string $relationship = 'studentExtracurricular';

    protected static ?string $title = 'Peserta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                // tampilkan semua student berdasarkan studentgrade
                Select::make('student_id')
                    ->label(__('extracurricular.student'))
                    ->multiple()
                    ->searchable()
                    ->live()
                    ->reactive()
                    ->options(function () {
                            // tampilkan semua student yang belum memiliki student_extracurricular
                            $students = Student::query()
                                ->whereDoesntHave('studentExtracurricular', function ($query) {
                                    $query->where('extracurricular_id', $this->ownerRecord->id);
                                })
                                ->pluck('name', 'id');

                            return $students;
                        }
                    )
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('extracurricular.student')),
                // input score berupa select
                SelectColumn::make('score')
                    ->label(__('extracurricular.score'))
                    ->options(LinkertScaleEnum::class),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->using(function (array $data, string $model): Model {
                        // academic_year_id
                        $academicYearId = $data['academic_year_id'];
                        $studentIds = $data['student_id'];

                        // ambil student_id pertama dan hapus dari array
                        $firstStudentId = array_shift($studentIds);

                        // buat record pertama update or create
                        $record = $model::updateOrCreate(
                            [
                                'academic_year_id' => $academicYearId,
                                'student_id' => $firstStudentId,
                                'extracurricular_id' => $this->ownerRecord->id,
                            ],
                            [
                                'academic_year_id' => $academicYearId,
                                'student_id' => $firstStudentId,
                                'extracurricular_id' => $this->ownerRecord->id,
                            ]
                        );

                        // buat record untuk student_id lainnya
                        foreach ($studentIds as $studentId) {
                            $model::updateOrCreate(
                                [
                                    'academic_year_id' => $academicYearId,
                                    'student_id' => $studentId,
                                    'extracurricular_id' => $this->ownerRecord->id,
                                ],
                                [
                                    'academic_year_id' => $academicYearId,
                                    'student_id' => $studentId,
                                    'extracurricular_id' => $this->ownerRecord->id,
                                ]
                            );
                        }

                        return $record;
                    }),
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
            ->paginated(false);
    }
}
