<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'grade',
        'phase',
        'is_inclusive',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Grade')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*'])
            ->logExcept(['created_at', 'updated_at']);
    }
    
    public function teacherGrade()
    {
        return $this->hasMany(TeacherGrade::class);
    }

    public function teacherGradeFirst()
    {
        return $this->hasOne(TeacherGrade::class)->where('academic_year_id', session('academic_year_id'));
    }

    public function studentGrade()
    {
        return $this->hasMany(StudentGrade::class);
    }
}
