<?php

namespace App\Models;

use App\Observers\CompetencyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Helpers\ScoreCriteriaHelper;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[ObservedBy([CompetencyObserver::class])]
class Competency extends Model
{
    use HasFactory;
    use LogsActivity;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'teacher_subject_id',
        'code',
        'description',
        'passing_grade',
        'half_semester',
        'code_skill',
        'description_skill',
    ];  

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
