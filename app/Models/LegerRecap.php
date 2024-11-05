<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_subject_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }   

}
