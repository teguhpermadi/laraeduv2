<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use App\Models\Scopes\OrderStudentScope;
use App\Observers\StudentGradeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
#[ObservedBy(StudentGradeObserver::class)]
class StudentGrade extends Model
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
        'academic_year_id',
        'student_id',
        'grade_id',
    ];  

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Student Grade')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*']);
    }

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function studentCompetency()
    {
        return $this->hasMany(StudentCompetency::class, 'student_id', 'student_id');
    }

    public function scopeMyGrade(Builder $query)
    {
        // ambil data berdasarkan teacher grade 
        $teacherGrade = TeacherGrade::query()->myGrade()->get()->pluck('grade_id');

        if (!$teacherGrade) {
            abort(403, 'Anda belum memiliki kelas yang ditugaskan');
        }   

        return $query->whereIn('grade_id', $teacherGrade);
    }

    public function project()
    {
        return $this->hasMany(Project::class, 'grade_id', 'grade_id');
    }
}
