<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('transcript_weight.weight_report1', 60);
        $this->migrator->add('transcript_weight.weight_written_exam1', 30);
        $this->migrator->add('transcript_weight.weight_practical_exam1', 10);
        $this->migrator->add('transcript_weight.weight_report2', 70);
        $this->migrator->add('transcript_weight.weight_written_exam2', 20);
        $this->migrator->add('transcript_weight.weight_practical_exam2', 10);
        $this->migrator->add('transcript_weight.weight_report', null);
        $this->migrator->add('transcript_weight.weight_written_exam', null);
        $this->migrator->add('transcript_weight.weight_practical_exam', null);
    }
};
