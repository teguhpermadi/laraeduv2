<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\AcademicYearScope;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class Attitude extends Model
{
    use HasFactory;

    protected $guarded = [];

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
