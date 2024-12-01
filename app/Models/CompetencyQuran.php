<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use App\Observers\CompetencyQuranObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


#[ObservedBy([CompetencyQuranObserver::class])]
class CompetencyQuran extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'teacher_quran_grade_id',
        'code',
        'description',
        'passing_grade',
    ];


    public function teacherQuranGrade()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function studentCompetencyQuran()
    {
        return $this->hasMany(StudentCompetencyQuran::class, 'competency_quran_id', 'id');
    }
}
