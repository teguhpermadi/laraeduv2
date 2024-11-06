<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attitude extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'grade_id',
        'student_id',
        'attitude_religius',
        'attitude_social',
    ];

    protected static function booted(): void
    {
        $academic_year_id = session()->get('academic_year_id');

        // buatkan global scope berdasarkan academic year id
        static::addGlobalScope('academicYear', function (Builder $builder) use ($academic_year_id) {
            $builder->where('academic_year_id', $academic_year_id);
        });

    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeMyGrade(Builder $query)
    {
        $grade = TeacherGrade::myGrade()->first();
        $students = $grade->grade->studentGrade->pluck('student_id');
        $query->whereIn('student_id', $students);
    }
}
