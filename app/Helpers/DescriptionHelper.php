<?php

namespace App\Helpers;

use App\Enums\CategoryLegerEnum;

class DescriptionHelper
{
    public static function getDescription($data)
    {
        $string = '';
        $string_skill = '';

        $filter = collect($data)->reject(function ($item) {
            $code = strtolower($item['competency']['code']);
            return $code === CategoryLegerEnum::HALF_SEMESTER->value || $code === CategoryLegerEnum::FULL_SEMESTER->value;
        })->values();

        $intro = 'Alhamdulillah, ananda ' . $data->first()->student->name;
        $passed = ' telah menguasai materi: ';
        $notPassed = ' tetapi masih perlu peningkatan lagi pada materi: ';
        $countPassed = 0;
        $countNotPassed = 0;

        $passedSkill = ' telah menguasai keterampilan: ';
        $notPassedSkill = ' tetapi masih perlu peningkatan lagi pada keterampilan: ';
        $countPassedSkill = 0;
        $countNotPassedSkill = 0;

        foreach ($filter as $sc) {
            $competency = $sc->competency;
            // Check if aspect is knowledge (default to knowledge if aspect is not set or empty)
            $isKnowledge = !$competency->aspect || $competency->aspect === \App\Enums\CompetencyAspectEnum::KNOWLEDGE;

            if ($isKnowledge) {
                if ($sc->score >= $competency->passing_grade) {
                    $passed .= $competency->description . '; ';
                    $countPassed++;
                } else {
                    $notPassed .= $competency->description . '; ';
                    $countNotPassed++;
                }
            } else {
                if ($sc->score >= $competency->passing_grade) {
                    $passedSkill .= $competency->description . '; ';
                    $countPassedSkill++;
                } else {
                    $notPassedSkill .= $competency->description . '; ';
                    $countNotPassedSkill++;
                }
            }
        }

        if ($countPassed > 0) {
            $string .= $passed;
        }

        if ($countNotPassed > 0) {
            $string .= $notPassed;
        }

        if ($countPassedSkill > 0) {
            $string_skill .= $passedSkill;
        }

        if ($countNotPassedSkill > 0) {
            $string_skill .= $notPassedSkill;
        }

        return [
            'description' => $intro . $string,
            'description_skill' => ($string_skill) ? $intro . $string_skill : null,
        ];
    }
}
