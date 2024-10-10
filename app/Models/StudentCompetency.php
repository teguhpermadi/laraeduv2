<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentCompetency extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'teacher_subject_id',
        'competency_id',
        'student_id',
        'score',
    ];  

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Student Competency')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*'])
            ->logExcept(['created_at', 'updated_at']);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
