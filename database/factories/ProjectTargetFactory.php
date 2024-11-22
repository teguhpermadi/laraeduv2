<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Target;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectTarget>
 */
class ProjectTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $target = Target::with('dimention', 'element.value.subValue', 'subElement')->get()->random();
        $value = $target->element->value->first();
        $subValue = $value->subValue->first();

        return [
            'project_id' => Project::get()->random()->id,
            'phase' => $target->phase,
            'dimention_id' => $target->dimention->first()->id,
            'element_id' => $target->element->first()->id,
            'sub_element_id' => $target->subElement->first()->id,
            'value_id' => $value->first()->id,
            'sub_value_id' => $subValue->first()->id,
            'target_id' => $target->id,
        ];
    }
}
