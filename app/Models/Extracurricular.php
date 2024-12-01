<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extracurricular extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

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
