<?php

namespace App\Models;

use App\Enums\TranscriptEnum;
use App\Observers\TranscriptObserver;
use App\Settings\TranscriptWeight;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

// #[ObservedBy(TranscriptObserver::class)]
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

    protected $appends = [
        'averageDataset1',
        'averageDataset2',
    ];

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

    public function calculateAverage($weight_report = self::DEFAULT_WEIGHTS['report_score'], $weight_written_exam = self::DEFAULT_WEIGHTS['written_exam'], $weight_practical_exam = self::DEFAULT_WEIGHTS['practical_exam'])
    {
        $report_score = $this->report_score;
        $written_exam = $this->written_exam;
        $practical_exam = $this->practical_exam;

        $total_weight = $weight_report;

        if ($written_exam) {
            $total_weight += $weight_written_exam;
        }

        if ($practical_exam) {
            $total_weight += $weight_practical_exam;
        }

        // nilai rata-rata
        $average_score = ($report_score * $weight_report) +
            (($written_exam ?? 0) * $weight_written_exam) +
            (($practical_exam ?? 0) * $weight_practical_exam);

        return \round($average_score / $total_weight, 2);
    }

    public function getAverageDataset1Attribute()
    {
        // get transcript setting dataset 1
        $transcriptWeightSetting = app(TranscriptWeight::class);
        $weight_report = $transcriptWeightSetting->weight_report1;
        $weight_written_exam = $transcriptWeightSetting->weight_written_exam1;
        $weight_practical_exam = $transcriptWeightSetting->weight_practical_exam1;

        return $this->calculateAverage($weight_report, $weight_written_exam, $weight_practical_exam);
    }

    public function getAverageDataset2Attribute()
    {
        // get transcript setting dataset 1
        $transcriptWeightSetting = app(TranscriptWeight::class);
        $weight_report = $transcriptWeightSetting->weight_report2;
        $weight_written_exam = $transcriptWeightSetting->weight_written_exam2;
        $weight_practical_exam = $transcriptWeightSetting->weight_practical_exam2;

        return $this->calculateAverage($weight_report, $weight_written_exam, $weight_practical_exam);
    }
}
