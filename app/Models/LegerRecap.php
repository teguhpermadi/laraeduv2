<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerRecap extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function leger()
    {
        return $this->hasMany(Leger::class, 'teacher_subject_id', 'teacher_subject_id');
    }
}
