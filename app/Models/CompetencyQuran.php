<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use App\Observers\CompetencyQuranObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


#[ObservedBy([CompetencyQuranObserver::class])]
class CompetencyQuran extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function teacherQuranGrade()
    {
        return $this->belongsTo(TeacherQuranGrade::class);
    }

    public function studentCompetencyQuran()
    {
        return $this->hasMany(StudentCompetencyQuran::class, 'competency_quran_id', 'id');
    }
}
