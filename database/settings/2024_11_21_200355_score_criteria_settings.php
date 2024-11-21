<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('score_criteria.grade_a', 85);
        $this->migrator->add('score_criteria.grade_b', 75);
        $this->migrator->add('score_criteria.grade_c', 65);
        $this->migrator->add('score_criteria.grade_d', 55);
    }
};
