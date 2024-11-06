<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'grade_id',
        'student_id',
        'sick',
        'permission',
        'absent',
        'note',
        'achievement',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
        static::addGlobalScope('totalAttendance', function (Builder $builder) {
            $builder->select(['*', DB::raw('sick + permission + absent as total_attendance')]);
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
