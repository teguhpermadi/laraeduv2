<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extracurricular extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_required',
    ];

    public function teacherExtracurricular()
    {
        return $this->hasMany(TeacherExtracurricular::class);
    }

    public function studentExtracurricular()
    {
        return $this->hasMany(StudentExtracurricular::class);
    }
}
