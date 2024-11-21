<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ScoreCriteriaSettings extends Settings
{
    public int $grade_a;
    public int $grade_b;
    public int $grade_c;
    public int $grade_d;

    public static function group(): string
    {
        return 'score_criteria';
    }
}