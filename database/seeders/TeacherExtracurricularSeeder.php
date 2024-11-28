<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\Teacher;
use App\Models\TeacherExtracurricular;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherExtracurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = TeacherExtracurricular::factory(10)->make()->toArray();

        foreach ($data as $item) {
            try {
                TeacherExtracurricular::create($item);
            } catch (\Throwable $th) {
                //
            }
        }
    }
}
