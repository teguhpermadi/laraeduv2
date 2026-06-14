<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TeacherWeightSetting extends Settings
{
    public bool $can_edit_weight = false;

    public static function group(): string
    {
        return 'teacher_weight';
    }
}
