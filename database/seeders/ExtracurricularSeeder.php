<?php

namespace Database\Seeders;

use App\Models\Extracurricular;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtracurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //buat 5   data dummy
        Extracurricular::create([
            'name' => 'Basket',
            'teacher_id' => 1,
            'is_required' => false,
        ]);

        Extracurricular::create([
            'name' => 'Voli',
            'teacher_id' => 1,
            'is_required' => false,
        ]);

        Extracurricular::create([
            'name' => 'Paskibraka',
            'teacher_id' => 1,
            'is_required' => false,
        ]);

        Extracurricular::create([
            'name' => 'Pramuka',
            'teacher_id' => 1,
            'is_required' => true,
        ]);
    }
}
