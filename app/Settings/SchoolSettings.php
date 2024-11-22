<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SchoolSettings extends Settings
{
    public string $school_name;
    public string $school_address;
    public string $school_nsm;
    public string $school_npsn;
    public string $school_email;
    public string $school_website;
    public string $school_phone;
    public string $school_logo;
    public string $school_level;

    public static function group(): string
    {
        return 'school';
    }
}