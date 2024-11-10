<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class QuranGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
    ];

    public function teacherQuranGrades(): HasMany
    {
        return $this->hasMany(TeacherQuranGrade::class);
    }

    public function studentQuranGrades(): HasMany
    {
        return $this->hasMany(StudentQuranGrade::class);
    }   
}
