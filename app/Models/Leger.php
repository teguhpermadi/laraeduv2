<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
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
        'is_half_semester',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];


    // booted academic year
    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }

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
