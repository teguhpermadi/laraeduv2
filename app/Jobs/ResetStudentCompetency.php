<?php

namespace App\Jobs;

use App\Models\StudentCompetency;
use App\Models\TeacherSubject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResetStudentCompetency implements ShouldQueue
{
    // use Queueable;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $teacher_subject_id;
    protected $competency_id;

    /**
     * Create a new job instance.
     */
    public function __construct($teacher_subject_id, $competency_id)
    {
        $this->teacher_subject_id = $teacher_subject_id;
        $this->competency_id = $competency_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // get students
        $students = TeacherSubject::with('studentGrade')
                        ->find($this->teacher_subject_id)
                        ->studentGrade->pluck('student_id');

        // delete student competency
        StudentCompetency::where('teacher_subject_id', $this->teacher_subject_id)
            ->where('competency_id', $this->competency_id)
            ->delete();
        
        // create new student competency
        $data = [];
        foreach ($students as $student) {
            $data[] = [
                'teacher_subject_id' => $this->teacher_subject_id,
                'student_id' => $student,
                'competency_id' => $this->competency_id,
                'created_at' => now(),
            ];
        }

        StudentCompetency::insert($data);
    }
}
