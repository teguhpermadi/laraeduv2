<?php

namespace Database\Seeders;

use App\Models\Competency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competencies = Competency::factory(5)->make()->toArray();
        foreach ($competencies as $competency) {
            Competency::updateOrCreate($competency);
        }
    }
}
