<?php

namespace App\Models;

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
        $academic_year_id = session()->get('academic_year_id');

        // buatkan global scope berdasarkan academic year id
        static::addGlobalScope('academicYear', function (Builder $builder) use ($academic_year_id) {
            $builder->where('academic_year_id', $academic_year_id);
        });

        static::addGlobalScope('totalAttendance', function (Builder $builder) use ($academic_year_id) {
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

        if (!$grade) {
            abort(403, 'Anda belum memiliki kelas yang ditugaskan');
        }

        $students = $grade->grade->studentGrade->pluck('student_id');
        $query->whereIn('student_id', $students);
    }
}
