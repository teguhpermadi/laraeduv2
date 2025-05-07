<?php

namespace App\Models;

use App\Enums\TranscriptEnum;
use App\Observers\TranscriptObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(TranscriptObserver::class)]
class Transcript extends Model
{
    use HasUlids;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'teacher_subject_id',
        'subject_id',
        'report_score',
        'written_exam',
        'practical_exam',
        'average_score',
        'metadata',
    ];

    /**
     * Default weights for score components (in percent, e.g., 60 means 60%).
     * These will be used if not specified in the metadata.
     */
    public const DEFAULT_WEIGHTS = [
        'report_score' => 60,
        'written_exam' => 30,
        'practical_exam' => 10,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'report_score' => 'float',
            'written_exam' => 'float',
            'practical_exam' => 'float',
            'average_score' => 'float',
            'metadata' => 'array',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }
}
