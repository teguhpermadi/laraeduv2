<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class ProjectNote extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

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
