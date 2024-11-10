<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetencyQuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_quran_grade_id',
        'code',
        'description',
        'passing_grade',
    ];


    public function teacherQuranGrade()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }
}
