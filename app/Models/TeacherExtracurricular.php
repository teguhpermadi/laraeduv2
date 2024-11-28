<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\TeacherExtracurricularObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(TeacherExtracurricularObserver::class)]
class TeacherExtracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'extracurricular_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function extracurricular()
    {
        return $this->belongsTo(Extracurricular::class);
    }   
}
