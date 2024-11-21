<?php

namespace App\Models;

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

    protected $fillable = [
        'name',
        'grade',
        'phase',
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
