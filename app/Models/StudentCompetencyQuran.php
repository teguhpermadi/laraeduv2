<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCompetencyQuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'quran_grade_id',
        'student_quran_grade_id',
        'competency_quran_id',
        'score',
    ];

    // tambahkan global scope academicyearscope
    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }


    public function studentQuranGrade()
    {
        return $this->belongsTo(StudentQuranGrade::class, 'student_quran_grade_id', 'id');
    }

    public function competencyQuran()
    {
        return $this->belongsTo(CompetencyQuran::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherQuranGrade()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function quranGrade()
    {
        return $this->belongsTo(QuranGrade::class);
    }
}
