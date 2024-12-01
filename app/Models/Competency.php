<?php

namespace App\Models;

use App\Observers\CompetencyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Helpers\ScoreCriteriaHelper;


#[ObservedBy([CompetencyObserver::class])]
class Competency extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Competency')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*'])
            ->logExcept(['created_at', 'updated_at']);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function studentCompetency()
    {
        return $this->hasMany(StudentCompetency::class);
    }

    public function getScoreCriteria($score)
    {
        return ScoreCriteriaHelper::getScoreCriteria($score, $this->passing_grade);
    }
}
