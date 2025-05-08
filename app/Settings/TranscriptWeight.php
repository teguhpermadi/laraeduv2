<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TranscriptWeight extends Settings
{
    public int $weight_report = 60;
    public int $weight_written_exam = 30;
    public int $weight_practical_exam = 10;

    public static function group(): string
    {
        return 'transcript_weight';
    }
}