<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Teacher extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'gender',
        'signature',
        'nip',
        'nuptk',
        'photo',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Teacher')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*']);
    }

    public function getSignatureUrlAttribute()
    {
        if (!$this->signature) {
            return null;
        }

        return Storage::url($this->signature);
    }

    public function academicYear()
    {
        return $this->hasMany(AcademicYear::class);
    }
}
