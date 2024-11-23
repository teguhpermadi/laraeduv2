<?php

namespace App\Helpers;

use App\Enums\CategoryLegerEnum;

class DescriptionHelper
{
    public static function getDescription($student,$data)
    {
        $string = '';
        $string_skill = '';

        $filter = collect($data)->reject(function ($item) {
            $code = strtolower($item['competency']['code']);
            return $code === CategoryLegerEnum::HALF_SEMESTER->value || $code === CategoryLegerEnum::FULL_SEMESTER->value;
        })->values();

        $intro = 'Alhamdulillah, ananda ' . $student->name;
        $passed = 'telah menguasai materi: ';
        $notPassed = 'perlu peningkatan lagi pada materi: ';
        $countPassed = 0;
        $countNotPassed = 0;

        $passedSkill = 'telah menguasai keterampilan: ';
        $notPassedSkill = 'perlu peningkatan lagi pada keterampilan: ';
        $countPassedSkill = 0;
        $countNotPassedSkill = 0;

        foreach ($filter as $competency) {
            if ($competency->score >= $competency->competency->passing_grade) {
                $passed .= $competency->competency->description . '; ';
                $countPassed++;
            } else {
                $notPassed .= $competency->competency->description . '; ';
                $countNotPassed++;
            }

            // jika competency skill ada isinya
            if ($competency->score_skill) {
                if ($competency->score_skill >= $competency->competency->passing_grade) {
                    $passedSkill .= $competency->competency->description_skill . '; ';
                    $countPassedSkill++;
                } else {
                    $notPassedSkill .= $competency->competency->description_skill . '; ';
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
