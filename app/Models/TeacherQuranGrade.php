<?php

namespace App\Models;

use App\Helpers\ScoreCriteriaHelper;
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

    public function competencyQuran(): HasMany
    {
        return $this->hasMany(CompetencyQuran::class);
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

    public function getScoreCriteria($score)
    {
        return ScoreCriteriaHelper::getScoreCriteria($score, $this->quranGrade->passing_grade);
    }

    public function legerQuran(): HasMany
    {
        return $this->hasMany(LegerQuran::class);
    }

    public function legerQuranRecap(): HasMany
    {
        return $this->hasMany(LegerQuranRecap::class);
    }
}
