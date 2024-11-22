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
        $projects = Project::factory()->count(10)->make();

        foreach ($projects as $project) {
            $target = Target::with('dimention', 'element.value.subValue', 'subElement')
                ->get()
                ->random();
            
            $value = $target->element->value->first();
            $subValue = $value->subValue->first();

            $data = [
                'project_id' => $project->id,
                'phase' => $project->phase,
                'dimention_id' => $target->dimention_id,
                'element_id' => $target->element_id,
                'sub_element_id' => $target->sub_element_id,
                'value_id' => $value->id,
                'sub_value_id' => $subValue->id,
                'target_id' => $target->id,
            ];

            ProjectTarget::create($data);
        }
    }
}
