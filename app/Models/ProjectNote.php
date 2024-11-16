<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class ProjectNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'project_id',
        'student_id',
        'note',
    ];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
