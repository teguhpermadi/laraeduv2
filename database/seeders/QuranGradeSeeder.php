<?php

namespace Database\Seeders;

use App\Models\QuranGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuranGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuranGrade::create([
            'name' => 'Jilid 1',
            'level' => 1
        ]);
        QuranGrade::create([
            'name' => 'Jilid 2',
            'level' => 2
        ]);
        QuranGrade::create([
            'name' => 'Jilid 3',
            'level' => 3
        ]);
    }
}
