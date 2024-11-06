<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'project_id',
        'student_id',
        'note',
    ];

    
    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
