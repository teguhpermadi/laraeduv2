<?php

namespace App\Filament\Resources\QuranGradeResource\RelationManagers;

use App\Models\Student;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class StudentQuranGradeRelationManager extends RelationManager
{
    protected static string $relationship = 'studentQuranGrade';

    // title
    protected static ?string $title = 'Siswa Mengaji';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('student_id')
                    ->label(__('quran-grade.fields.students.label'))
                    ->multiple()
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        $students = Student::query()
                            ->whereDoesntHave('studentQuranGrade', function ($query) {
                                $query->where('academic_year_id', session('academic_year_id'));
                            })
                            ->pluck('name', 'id');

                        return $students;
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('quran-grade.fields.students.label')),
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
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        // dd($data);
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
                                'quran_grade_id' => $this->ownerRecord->id,
                            ],
                            [
                                'academic_year_id' => $academicYearId,
                                'student_id' => $firstStudentId,
                                'quran_grade_id' => $this->ownerRecord->id,
                            ]
                        );

                        // buat record untuk student_id lainnya
                        foreach ($studentIds as $studentId) {
                            $model::create([
                                'academic_year_id' => $academicYearId,
                                'student_id' => $studentId,
                                'quran_grade_id' => $this->ownerRecord->id,
                            ]);
                        }

                        return $record;
                    }),
            ])
            ->paginated(false);
    }
}
