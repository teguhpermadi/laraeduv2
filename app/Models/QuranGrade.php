<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class QuranGrade extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'level',
    ];

    public function teacherQuranGrade(): HasMany
    {
        return $this->hasMany(TeacherQuranGrade::class);
    }

    public function studentQuranGrade(): HasMany
    {
        return $this->hasMany(StudentQuranGrade::class);
    }   
}
