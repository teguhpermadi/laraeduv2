<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerQuranRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_quran_grade_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherQuranGrade()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function leger()
    {
        return $this->hasMany(LegerQuran::class, 'teacher_quran_grade_id', 'teacher_quran_grade_id');
    }
}
