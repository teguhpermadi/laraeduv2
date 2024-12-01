<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerQuran extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'academic_year_id',
        'student_id',
        'quran_grade_id',
        'teacher_quran_grade_id',
        'score',
        'description',
        'metadata',
        'sum',
        'rank',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacherQuran()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function quranGrade()
    {
        return $this->belongsTo(QuranGrade::class);
    }

    public function quranNote()
    {
        return $this->hasOne(LegerQuranNote::class);
    }
}
