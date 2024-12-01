<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerQuranRecap extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'academic_year_id',
        'teacher_quran_grade_id',
        'updated_at',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherQuranGrade()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function leger()
    {
        return $this->hasMany(LegerQuran::class, 'teacher_quran_grade_id', 'teacher_quran_grade_id');
    }
}
