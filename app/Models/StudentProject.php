<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class StudentProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'project_target_id',
        'score',
    ];

    public function projectTarget()
    {
        return $this->belongsTo(ProjectTarget::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }  

    public function projectNote()
    {
        return $this->hasOne(ProjectNote::class, 'student_id', 'student_id');
    }
}
