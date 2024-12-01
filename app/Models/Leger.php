<?php

namespace App\Models;

use App\Enums\CategoryLegerEnum;
use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[ScopedBy([OrderStudentScope::class])]
class Leger extends Model
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
        'teacher_subject_id',
        'teacher_id',
        'subject_id',
        'subject_order',
        'score',
        'description',
        'metadata',
        'sum',
        'rank', 
        'category',
        'score_skill',
        'sum_skill',
        'description_skill',
        'passing_grade',
    ];

    protected $casts = [
        'metadata' => 'array',
        'category' => CategoryLegerEnum::class,
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function studentGrade()
    {
        return $this->hasMany(StudentGrade::class, 'student_id', 'student_id');
    }

    public function note()
    {
        return $this->hasOne(LegerNote::class);
    }
}
