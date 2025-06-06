<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
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
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nisn',
        'nis',
        'name',
        'gender',
        'active',
        'city_born',
        'birthday',
        'nick_name',
        'photo',
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
            $builder->orderBy('id', 'desc');
        });
    }

    public function studentGrade()
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function studentGradeFirst()
    {
        return $this->hasOne(StudentGrade::class)->where('academic_year_id', session('academic_year_id'));
    }

    public function attendanceFirst()
    {
        return $this->hasOne(Attendance::class)->where('academic_year_id', session('academic_year_id'));
    }

    public function attitudeFirst()
    {
        return $this->hasOne(Attitude::class)->where('academic_year_id', session('academic_year_id'));
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

    // leger
    public function leger()
    {
        return $this->hasMany(Leger::class)->orderBy('subject_order', 'asc');
    }

    // legerquran
    public function legerQuran()
    {
        return $this->hasMany(LegerQuran::class);
    }

    // attitude
    public function attitude()
    {
        return $this->hasMany(Attitude::class);
    }

    // attendance
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    // extracurricular
    public function extracurricular()
    {
        return $this->hasMany(StudentExtracurricular::class)->with('extracurricular');
    }

    public function project()
    {
        return $this->hasMany(StudentProject::class);
    }

    public function studentCompetencyQuran()
    {
        return $this->hasMany(StudentCompetencyQuran::class);
    }

    public function studentCompetency()
    {
        return $this->hasMany(StudentCompetency::class);
    }

    public function inactive()
    {
        return $this->hasOne(StudentInactive::class);
    }

    public function transcript()
    {
        return $this->hasMany(Transcript::class);
    }
}
