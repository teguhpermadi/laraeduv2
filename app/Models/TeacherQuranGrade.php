<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TeacherQuranGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'quran_grade_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }   

    public function quranGrade(): BelongsTo
    {
        return $this->belongsTo(QuranGrade::class);
    }   

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studentQuranGrade(): HasMany
    {
        return $this->hasMany(StudentQuranGrade::class, 'quran_grade_id', 'quran_grade_id');
    }

    // scope my quran grade
    public function scopeMyQuranGrade($query)
    {
        return $query->where('teacher_id', auth()->user()->userable->userable_id);
    }
}
