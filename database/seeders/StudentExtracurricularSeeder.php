<?php

namespace Database\Seeders;

use App\Models\StudentExtracurricular;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentExtracurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = StudentExtracurricular::factory(50)->make()->toArray();

        foreach ($data as $item) {
            try {
                StudentExtracurricular::create($item);
            } catch (\Throwable $th) {
                //
            }
        }
    }
}
