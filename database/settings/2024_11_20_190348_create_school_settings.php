<?php

use App\Enums\SchoolLevelEnum;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('school.school_name', 'Sekolah ABC');
        $this->migrator->add('school.school_address', 'Jalan raya');
        $this->migrator->add('school.school_email', 'school@laraedu.com');
        $this->migrator->add('school.school_website', 'www.laraedu.com');
        $this->migrator->add('school.school_phone', '081234567890');
        $this->migrator->add('school.school_nsm', '12345678');
        $this->migrator->add('school.school_npsn', '12345678');
        $this->migrator->add('school.school_logo', 'logo.png');
        $this->migrator->add('school.school_level', SchoolLevelEnum::SD->value);
    }
};
