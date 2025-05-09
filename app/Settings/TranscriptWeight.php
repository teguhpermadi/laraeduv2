<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TranscriptWeight extends Settings
{
    public int $weight_report1 = 60;
    public int $weight_written_exam1 = 30;
    public int $weight_practical_exam1 = 10;
    public int $weight_report2 = 70;
    public int $weight_written_exam2 = 20;
    public int $weight_practical_exam2 = 10;
    public $weight_report = null;
    public $weight_written_exam = null;
    public $weight_practical_exam = null;

    public static function group(): string
    {
        return 'transcript_weight';
    }
}