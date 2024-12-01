<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectTarget;
use App\Models\Target;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ProjectTarget::factory(10)->create();
        $target = Target::with('dimention', 'element.value.subValue', 'subElement')->get()->random();
        $value = $target->element->value->first();
        $subValue = $value->subValue->first();
        $project_id = Project::get()->random();

        for ($i=0; $i < 10; $i++) { 
            $data[] = [
                'id' => Str::ulid()->toBase32(),
                'project_id' => $project_id->id,
                'phase' => $target->phase,
                'dimention_id' => $target->dimention->first()->id,
                'element_id' => $target->element->first()->id,
                'sub_element_id' => $target->subElement->first()->id,
                'value_id' => $value->first()->id,
                'sub_value_id' => $subValue->first()->id,
                'target_id' => $target->id,
            ];
            
        }
        
        ProjectTarget::insert($data);
    }
}
