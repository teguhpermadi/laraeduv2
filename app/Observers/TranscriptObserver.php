<?php

namespace App\Observers;

use App\Models\Transcript;

class TranscriptObserver
{
    /**
     * Handle the Transcript "saving" event.
     *
     * @param  \App\Models\Transcript  $transcript
     * @return void
     */
    public function saving(Transcript $transcript): void
    {
        // Logika yang sama dari model, dipindahkan ke sini
        $configWeights = $transcript->metadata['weights'] ?? Transcript::DEFAULT_WEIGHTS;

        $weightReport = is_numeric($configWeights['report_score'] ?? Transcript::DEFAULT_WEIGHTS['report_score'])
            ? (float)($configWeights['report_score'] ?? Transcript::DEFAULT_WEIGHTS['report_score'])
            : (float)Transcript::DEFAULT_WEIGHTS['report_score'];

        $weightWritten = is_numeric($configWeights['written_exam'] ?? Transcript::DEFAULT_WEIGHTS['written_exam'])
            ? (float)($configWeights['written_exam'] ?? Transcript::DEFAULT_WEIGHTS['written_exam'])
            : (float)Transcript::DEFAULT_WEIGHTS['written_exam'];

        $weightPractical = is_numeric($configWeights['practical_exam'] ?? Transcript::DEFAULT_WEIGHTS['practical_exam'])
            ? (float)($configWeights['practical_exam'] ?? Transcript::DEFAULT_WEIGHTS['practical_exam'])
            : (float)Transcript::DEFAULT_WEIGHTS['practical_exam'];

        $reportScore = is_numeric($transcript->report_score) ? (float)$transcript->report_score : 0;
        $writtenExam = is_numeric($transcript->written_exam) ? (float)$transcript->written_exam : 0;
        $practicalExam = is_numeric($transcript->practical_exam) ? (float)$transcript->practical_exam : 0;

        $totalWeightPercentage = $weightReport + $weightWritten + $weightPractical;

        if ($totalWeightPercentage <= 0) {
            $transcript->average_score = null;
        } else {
            $average = (
                ($reportScore * ($weightReport / $totalWeightPercentage)) +
                ($writtenExam * ($weightWritten / $totalWeightPercentage)) +
                ($practicalExam * ($weightPractical / $totalWeightPercentage))
            );
            $transcript->average_score = round($average, 2);
        }
    }

    /**
     * Handle the Transcript "created" event.
     */
    public function created(Transcript $transcript): void
    {
        //
    }

    /**
     * Handle the Transcript "updated" event.
     */
    public function updated(Transcript $transcript): void
    {
        //
    }

    /**
     * Handle the Transcript "deleted" event.
     */
    public function deleted(Transcript $transcript): void
    {
        //
    }

    /**
     * Handle the Transcript "restored" event.
     */
    public function restored(Transcript $transcript): void
    {
        //
    }

    /**
     * Handle the Transcript "force deleted" event.
     */
    public function forceDeleted(Transcript $transcript): void
    {
        //
    }
}
