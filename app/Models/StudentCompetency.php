<?php

namespace App\Models;

use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([OrderStudentScope::class])]
class StudentCompetency extends Model
{
    use HasFactory;
    use HasUlids;
    use LogsActivity;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'teacher_subject_id',
        'competency_id',
        'student_id',
        'score',
        'score_skill',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Student Competency')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*'])
            ->logExcept(['created_at', 'updated_at']);
    }

    public function student()
    {
        return $this->belongsTo(Student::class)->withoutGlobalScope(\App\Models\Scopes\StudentActiveScope::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class, 'teacher_subject_id', 'id')->withoutGlobalScope(\App\Models\Scopes\AcademicYearScope::class);
    }

    public function getScoreCriteria()
    {
        // Pastikan relasi competency sudah ter-load
        $competency = $this->competency;

        if ($competency) {
            return $competency->getScoreCriteria($this->score);
        }

        return null; // Atau bisa mengembalikan nilai default jika competency tidak ditemukan
    }

    public function studentRdm()
    {
        return $this->hasOne(StudentRdm::class);
    }
}
