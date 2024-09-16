<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AcademicYear extends Model
{
    use HasFactory, HasUlids;
    use LogsActivity;

    protected $fillable = [
        'id',
        'year',
        'semester',
        'teacher_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly([
            'year',
            'semester',
            'teacher_id',
        ]);
    }
}
