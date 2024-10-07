<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
}
