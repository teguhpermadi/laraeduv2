<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;

#[ScopedBy([OrderStudentScope::class])]
class Leger extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'teacher_subject_id',
        'teacher_id',
        'subject_id',
        'subject_order',
        'score',
        'description',
        'metadata',
        'sum',
        'rank', 
        'category',
        'score_skill',
        'sum_skill',
        'description_skill',
        'passing_grade',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function studentGrade()
    {
        return $this->hasMany(StudentGrade::class, 'student_id', 'student_id');
    }

    public function note()
    {
        return $this->hasOne(LegerNote::class);
    }
}
