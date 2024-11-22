<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectTarget;
use App\Models\Target;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory(5)->create();
    }
}
