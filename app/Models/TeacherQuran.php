<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherQuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
    ];
}
