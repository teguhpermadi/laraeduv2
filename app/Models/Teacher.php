<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Teacher extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

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

    public function userable()
    {
        return $this->morphOne(Userable::class, 'userable');
    }

    public function subject()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function teacherGrade()
    {
        return $this->hasMany(TeacherGrade::class);
    }

    public function teacherExtracurricular()
    {
        return $this->hasMany(TeacherExtracurricular::class);
    }

    public function teacherQuran()
    {
        return $this->hasMany(TeacherQuran::class);
    }
}
