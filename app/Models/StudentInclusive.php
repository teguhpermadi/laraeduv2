<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInclusive extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'teacher_id',
        'category_inclusive',
    ];

    // category_inclusive adalah array json
    protected $casts = [
        'category_inclusive' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
