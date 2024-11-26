<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubjectNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_subject_id',
        'note',
    ];

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }
}
