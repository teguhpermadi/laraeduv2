<?php

namespace Database\Seeders;

use App\Models\TeacherGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = TeacherGrade::factory(10)->make()->toArray();

        foreach ($data as $item) {
            try {   
                TeacherGrade::create($item);
            } catch (\Throwable $th) {
                //
            }
        }
    }
}
