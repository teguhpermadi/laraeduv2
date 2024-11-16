<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
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
