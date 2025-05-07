<?php

namespace App\Filament\Resources\TranscriptResource\Pages;

use App\Enums\CategoryLegerEnum;
use App\Enums\TranscriptEnum;
use App\Filament\Resources\TranscriptResource;
use App\Helpers\IdHelper;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\TeacherSubject;
use App\Models\Transcript;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTranscripts extends ListRecords
{
    protected static string $resource = TranscriptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('preview')
                ->label('Preview')
                ->url(fn () => route('transcript-preview')),
            Action::make('sycn')
                ->label('Sync')
                ->slideOver()
                ->form([
                    Select::make('grade_id')
                        ->label('Grade')
                        ->live()
                        ->options(Grade::all()->pluck('name', 'id'))
                        ->required(),
                    Select::make('academic_year_id')
                        ->label('Academic Year')
                        ->live()
                        ->multiple()
                        ->options(AcademicYear::all()->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'year' => $item->year . ' - ' . $item->semester,
                            ];
                        })->pluck('year', 'id'))
                        ->required(),
                    Select::make('student_ids')
                        ->reactive()
                        ->multiple()
                        ->options(function (callable $get) {
                            $studentGrade = StudentGrade::where('grade_id', $get('grade_id'))
                                ->where('academic_year_id', $get('academic_year_id'))
                                ->with('student')
                                ->get();

                            return $studentGrade->pluck('student.name', 'student.id');
                        })
                        ->required()
                        ->hintAction(
                            fn(Select $component) => ActionsAction::make('select all')
                                ->action(function (callable $get) use ($component) {
                                    $studentGrade = StudentGrade::where('grade_id', $get('grade_id'))
                                        ->where('academic_year_id', $get('academic_year_id'))
                                        ->get();

                                    return $component->state($studentGrade->pluck('student_id')->toArray());
                                })
                        ),
                ])
                ->action(function ($data) {
                    $studentIds = $data['student_ids'];
                    $academicYearIds = $data['academic_year_id'];
                    $gradeId = $data['grade_id'];

                    $students = Student::with(['leger' => function ($query) use ($academicYearIds) {
                        $query->with('academicYear');
                        $query->select('id', 'student_id', 'subject_id', 'academic_year_id','teacher_subject_id', 'score', 'score_skill');
                        $query->where('category', CategoryLegerEnum::FULL_SEMESTER->value);
                        $query->whereIn('academic_year_id', $academicYearIds);
                    }])
                        ->whereIn('id', $studentIds)
                        ->get();

                    $data = [];
                    foreach ($students as $student) { // setiap siswa
                        $legers = $student->leger->groupBy('subject.id');
                        // $data[$student->id] = [
                        //     'student_id' => $student->id,
                        //     'academic_year_id' => session()->get('academic_year_id'),
                        //     'score' => $legers->average('score'),
                        //     'metadata' => $legers->toArray(),
                        // ];
                        
                        foreach ($legers as $i => $leger) { // setiap mata pelajaran
                            
                            // $data[$student->id][$i] = [
                            //     'student_id' => $student->id,
                            //     'academic_year_id' => session()->get('academic_year_id'),
                            //     'subject_id' => $i,
                            //     'score' => $leger->average('score'),
                            //     'metadata' => $leger->toArray(),
                            // ];
                            $teacherSubject = TeacherSubject::where('academic_year_id', session()->get('academic_year_id'))
                                    ->where('subject_id', $i)
                                    ->where('grade_id', $gradeId)
                                    ->first();

                            $param = $student->id . $i;
                            $data = [
                                'id' => IdHelper::deterministicUlidLike($param),
                                'student_id' => $student->id,
                                'academic_year_id' => session()->get('academic_year_id'),
                                'teacher_subject_id' => $teacherSubject->id,
                                'subject_id' => $i,
                                'report_score' => $leger->average('score'),
                                'written_exam' => \random_int(50, 100),
                                'practical_exam' => \random_int(50, 100),
                            ];

                            Transcript::updateOrCreate(['id' => $data['id']], $data); // dd($data);
                        }
                        
                    }

                    // dd($data);
                    // Transcript::upsert($data, uniqueBy:['id'], update:['report_score', 'written_exam', 'practical_exam']);

                    Notification::make()
                        ->title('Sync Success')
                        ->success()
                        ->send();
                }),
        ];
    }
}
