<?php

namespace App\Models;

use App\Observers\TeacherSubjectObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(TeacherSubjectObserver::class)]
class TeacherSubject extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'subject_id',
        'grade_id',
        'time_allocation',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Teacher Subject')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
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
        return $this->hasMany(StudentGrade::class, 'grade_id', 'grade_id');
    }

    public function competency()
    {
        return $this->hasMany(Competency::class, 'teacher_subject_id');
    }

    public function scopeMySubject(Builder $query, $teacher_id = null):void 
    {
        if(is_null($teacher_id)){
            $teacher_id = auth()->user()->userable->userable_id;
        }

        // jika user tidak memiliki pelajaran yang ditugaskan
        $hasSubject = self::where('teacher_id', $teacher_id)->whereNotNull('subject_id')->exists();

        if (!$hasSubject) {
            abort(403, 'Anda belum memiliki pelajaran yang ditugaskan');
        }

        $query->where('teacher_id', $teacher_id)->with('subject');
    }

    public function scopeMySubjectByGrade(Builder $query, $grade_id, $teacher_id = null):void
    {
        if(is_null($teacher_id)){
            $teacher_id = auth()->user()->userable->userable_id;
        }

        $query->where('teacher_id', $teacher_id)
            ->where('grade_id', $grade_id)
            ->with('subject');
    }

    public function scopeMyGrade(Builder $query, $teacher_id = null):void
    {
        if(is_null($teacher_id)){
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
}
