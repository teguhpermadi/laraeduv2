<?php

namespace App\Models;

use App\Observers\CompetencyObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


#[ObservedBy([CompetencyObserver::class])]
class Competency extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
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
}
