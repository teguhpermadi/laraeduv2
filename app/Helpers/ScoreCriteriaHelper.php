<?php

namespace App\Helpers;

use App\Settings\ScoreCriteriaSettings;

class ScoreCriteriaHelper
{
    public static function getScoreCriteria($score, $passingGrade)
    {
        $settings = app(ScoreCriteriaSettings::class);

        if ($score >= $settings->grade_a) {
            return 'A';
        } elseif ($score >= $settings->grade_b) {
            return 'B';
        } elseif ($score >= $settings->grade_c) {
            return 'C';
        } elseif ($score >= $settings->grade_d) {
            return 'D';
        } else {
            return 'E';
        }
    }
} 