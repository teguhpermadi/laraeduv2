<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQuranGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'quran_grade_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }

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

}
