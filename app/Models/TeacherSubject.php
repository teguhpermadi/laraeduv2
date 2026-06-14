<?php

namespace App\Models;

use App\Enums\CategoryLegerEnum;
use App\Helpers\ScoreCriteriaHelper;
use App\Models\Scopes\AcademicYearScope;
use App\Observers\TeacherSubjectObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(TeacherSubjectObserver::class)]
#[ScopedBy(AcademicYearScope::class)]
class TeacherSubject extends Model
{
    use HasFactory;
    use HasUlids;
    use LogsActivity;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'academic_year_id',
        'teacher_id',
        'subject_id',
        'grade_id',
        'time_allocation',
        'passing_grade',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Teacher Subject')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*']);
    }

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function studentGrade()
    {
        return $this->hasMany(StudentGrade::class, 'grade_id', 'grade_id')
            // ->withoutGlobalScope(AcademicYearScope::class)
            ->orderBy('student_id', 'asc');
    }

    public function competency()
    {
        return $this->hasMany(Competency::class, 'teacher_subject_id');
    }

    public function scopeMySubject(Builder $query, $teacher_id = null): void
    {
        if (is_null($teacher_id)) {
            $teacher_id = auth()->user()->userable->userable_id;
        }

        // jika user tidak memiliki pelajaran yang ditugaskan
        $hasSubject = self::where('teacher_id', $teacher_id)->whereNotNull('subject_id')->exists();

        if (! $hasSubject) {
            abort(403, 'Anda belum memiliki pelajaran yang ditugaskan');
        }

        $query->where('teacher_id', $teacher_id)->with('subject');
    }

    public function scopeMySubjectByGrade(Builder $query, $grade_id, $teacher_id = null): void
    {
        if (is_null($teacher_id)) {
            $teacher_id = auth()->user()->userable->userable_id;
        }

        $query->where('teacher_id', $teacher_id)
            ->where('grade_id', $grade_id)
            ->with('subject');
    }

    public function scopeMyGrade(Builder $query, $teacher_id = null): void
    {
        if (is_null($teacher_id)) {
            $teacher_id = auth()->user()->userable->userable_id;
        }

        $query->where('teacher_id', $teacher_id)->with('grade');
    }

    public function leger()
    {
        return $this->hasMany(Leger::class, 'teacher_subject_id');
    }

    public function legerRecap()
    {
        return $this->hasMany(LegerRecap::class, 'teacher_subject_id');
    }

    public function legerRecapHalfSemester()
    {
        return $this->hasOne(LegerRecap::class, 'teacher_subject_id')
            ->where('category', CategoryLegerEnum::HALF_SEMESTER->value);
    }

    public function legerRecapFullSemester()
    {
        return $this->hasOne(LegerRecap::class, 'teacher_subject_id')
            ->where('category', CategoryLegerEnum::FULL_SEMESTER->value);
    }

    public function teacherGrade()
    {
        return $this->hasOne(TeacherGrade::class, 'grade_id', 'grade_id');
    }

    public function getScoreCriteria($score)
    {
        return ScoreCriteriaHelper::getScoreCriteria($score, $this->passing_grade);
    }

    public function note()
    {
        return $this->hasOne(TeacherSubjectNote::class);
    }

    public function legerWeight()
    {
        return $this->hasOne(LegerWeight::class, 'teacher_subject_id');
    }

    public function getActiveWeight(): ?LegerWeight
    {
        $custom = $this->legerWeight;
        if ($custom) {
            return $custom;
        }

        return LegerWeight::where('academic_year_id', $this->academic_year_id)
            ->whereNull('teacher_subject_id')
            ->first();
    }

    public function calculateLegerScore(Collection $studentCompetencies, string $category): array
    {
        $dailyCompetencies = $studentCompetencies->filter(fn ($sc) => $sc->competency && ! $sc->competency->half_semester && $sc->competency->code !== CategoryLegerEnum::FULL_SEMESTER->value
        );
        $midCompetencies = $studentCompetencies->filter(fn ($sc) => $sc->competency && $sc->competency->half_semester);
        $finalCompetencies = $studentCompetencies->filter(fn ($sc) => $sc->competency && $sc->competency->code === CategoryLegerEnum::FULL_SEMESTER->value);

        $weight = $this->getActiveWeight();

        $fallbackAvg = $studentCompetencies->avg('score');
        $fallbackAvgSkill = $studentCompetencies->avg('score_skill');

        if (! $weight || ($weight->daily_weight === 0 && $weight->mid_weight === 0 && $weight->final_weight === 0)) {
            return [
                'avg_score' => round($fallbackAvg, 0),
                'avg_skill' => round($fallbackAvgSkill, 0),
            ];
        }

        $dailyWeight = $weight->daily_weight;
        $midWeight = $weight->mid_weight;
        $finalWeight = $weight->final_weight;

        $dailyAvg = $dailyCompetencies->isNotEmpty() ? $dailyCompetencies->avg('score') : 0;
        $dailyAvgSkill = $dailyCompetencies->isNotEmpty() ? $dailyCompetencies->avg('score_skill') : 0;
        $midAvg = $midCompetencies->isNotEmpty() ? $midCompetencies->avg('score') : 0;
        $finalAvg = $finalCompetencies->isNotEmpty() ? $finalCompetencies->avg('score') : 0;

        if ($category === CategoryLegerEnum::HALF_SEMESTER->value) {
            $totalWeight = 0;
            $weightedSum = 0;

            if ($dailyWeight > 0 && $dailyCompetencies->isNotEmpty()) {
                $totalWeight += $dailyWeight;
                $weightedSum += $dailyAvg * $dailyWeight;
            }

            if ($midWeight > 0 && $midCompetencies->isNotEmpty()) {
                $totalWeight += $midWeight;
                $weightedSum += $midAvg * $midWeight;
            }

            $avgScore = $totalWeight > 0 ? $weightedSum / $totalWeight : ($fallbackAvg ?: 0);
            $avgSkill = $dailyCompetencies->isNotEmpty() ? $dailyAvgSkill : ($fallbackAvgSkill ?: 0);
        } else {
            $totalWeight = 0;
            $weightedSum = 0;

            if ($dailyWeight > 0 && $dailyCompetencies->isNotEmpty()) {
                $totalWeight += $dailyWeight;
                $weightedSum += $dailyAvg * $dailyWeight;
            }

            if ($finalWeight > 0 && $finalCompetencies->isNotEmpty()) {
                $totalWeight += $finalWeight;
                $weightedSum += $finalAvg * $finalWeight;
            }

            $avgScore = $totalWeight > 0 ? $weightedSum / $totalWeight : ($fallbackAvg ?: 0);
            $avgSkill = $dailyCompetencies->isNotEmpty() ? $dailyAvgSkill : ($fallbackAvgSkill ?: 0);
        }

        return [
            'avg_score' => round($avgScore, 0),
            'avg_skill' => round($avgSkill, 0),
        ];
    }
}
