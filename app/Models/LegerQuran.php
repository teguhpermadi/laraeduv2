<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerQuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'quran_grade_id',
        'teacher_quran_id',
        'score',
        'description',
        'metadata',
        'sum',
        'rank',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

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

    public function teacherQuran()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function quranGrade()
    {
        return $this->belongsTo(QuranGrade::class);
    }
}
