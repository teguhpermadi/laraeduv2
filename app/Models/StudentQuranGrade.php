<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class StudentQuranGrade extends Model
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
        'quran_grade_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function quranGrade(): BelongsTo
    {
        return $this->belongsTo(QuranGrade::class);
    }   

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studentCompetencyQuran(): HasMany
    {
        return $this->hasMany(StudentCompetencyQuran::class, 'student_id', 'student_id');
    }

}
