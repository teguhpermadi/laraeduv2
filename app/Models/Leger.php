<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leger extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'teacher_subject_id',
        'score',
        'description',
        'metadata',
        'sum',
        'rank', 
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
}
