<?php

namespace App\Jobs;

use App\Models\DataStudent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyDataStudentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dataStudents = DB::connection('laraedu')->table('data_students')->get();

        foreach ($dataStudents as $dataStudent) {
            $data = [
                'student_id' => $dataStudent->student_id,
                'student_address' => $dataStudent->student_address,
                'student_province' => $dataStudent->student_province,
                'student_city' => $dataStudent->student_city,
                'student_district' => $dataStudent->student_district,
                'student_village' => $dataStudent->student_village,
                'religion' => $dataStudent->religion,
                'previous_school' => $dataStudent->previous_school,
                'father_name' => $dataStudent->father_name,
                'father_education' => $dataStudent->father_education,
                'father_occupation' => $dataStudent->father_occupation,
                'father_phone' => $dataStudent->father_phone,
                'mother_name' => $dataStudent->mother_name,
                'mother_education' => $dataStudent->mother_education,
                'mother_occupation' => $dataStudent->mother_occupation,
                'mother_phone' => $dataStudent->mother_phone,
                'guardian_name' => $dataStudent->guardian_name,
                'guardian_education' => $dataStudent->guardian_education,
                'guardian_occupation' => $dataStudent->guardian_occupation,
                'guardian_phone' => $dataStudent->guardian_phone,
                'guardian_village' => null,
                'parent_address' => $dataStudent->parent_address,
                'parent_province' => $dataStudent->parent_province,
                'parent_city' => $dataStudent->parent_city,
                'parent_district' => $dataStudent->parent_district,
                'parent_village' => $dataStudent->parent_village,
                'date_received' => $dataStudent->date_received,
                'grade_received' => $dataStudent->grade_received,
            ];

            DataStudent::create($data);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyDataStudentJob failed: ' . $exception->getMessage());
    }
}
