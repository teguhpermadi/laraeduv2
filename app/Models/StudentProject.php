<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class StudentProject extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

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
        return $this->belongsTo(Student::class)->withoutGlobalScope(\App\Models\Scopes\StudentActiveScope::class);
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
