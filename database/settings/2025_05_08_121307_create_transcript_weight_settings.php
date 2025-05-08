<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('transcript_weight.weight_report', 60);
        $this->migrator->add('transcript_weight.weight_written_exam', 30);
        $this->migrator->add('transcript_weight.weight_practical_exam', 10);
    }
};
