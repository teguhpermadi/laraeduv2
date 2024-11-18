<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Student extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'nisn',
        'nis',
        'name',
        'gender',
        'active',
        'city_born',
        'birthday',
        'nick_name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Student')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*'])
            ->logExcept(['created_at', 'updated_at']);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', 1);
        });
        
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('id', 'asc');
        });
    }

    public function studentGrade()
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function dataStudent()
    {
        return $this->hasOne(DataStudent::class);
    }

    public function scopeMyStudentGrade(Builder $query, $teacher_id = null)
    {
        if(is_null($teacher_id)){
            $teacher_id = auth()->user()->userable->userable_id;
        }

        $grade = TeacherGrade::where('teacher_id', $teacher_id)->with('grade.StudentGrade')->first();

        if (!$grade) {
            abort(403, 'Anda belum memiliki kelas yang ditugaskan');
        }   
  
        $myStudents = $grade->grade->studentGrade->pluck('student_id');

        $query->whereIn('id', $myStudents);
    }

    public function studentExtracurricular()
    {
        return $this->hasMany(StudentExtracurricular::class);
    }   

    public function studentQuranGrade()
    {
        return $this->hasMany(StudentQuranGrade::class);
    }

    public function leger()
    {
        return $this->hasMany(Leger::class);
    }
}
