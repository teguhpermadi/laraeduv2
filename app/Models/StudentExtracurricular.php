<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExtracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'extracurricular_id',
        'academic_year_id',
        'score',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }   

    public function extracurricular()
    {
        return $this->belongsTo(Extracurricular::class);
    }   

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
