<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\OrderStudentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[ScopedBy([AcademicYearScope::class, OrderStudentScope::class])]
class Attendance extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'academic_year_id',
        'grade_id',
        'student_id',
        'sick',
        'permission',
        'absent',
        'note',
        'achievement',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected static function booted(): void
    {
        static::addGlobalScope('totalAttendance', function (Builder $builder) {
            $builder->select(['*', DB::raw('sick + permission + absent as total_attendance')]);
        });
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function scopeMyGrade(Builder $query)
    {
        $grades = TeacherGrade::myGrade()->get();

        if (!$grades) {
            abort(403, 'Anda belum memiliki kelas yang ditugaskan');
        }

        // $students = $grade->grade->studentGrade->pluck('student_id');
        $students = [];
        foreach ($grades as $grade) {
            foreach ($grade->studentGrade as $student) {
                $students[] = $student->student_id;
            }
        }

        $query->whereIn('student_id', $students);
    }
}
