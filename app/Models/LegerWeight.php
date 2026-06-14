<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerWeight extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'academic_year_id',
        'teacher_subject_id',
        'daily_weight',
        'mid_weight',
        'final_weight',
    ];

    protected function casts(): array
    {
        return [
            'daily_weight' => 'integer',
            'mid_weight' => 'integer',
            'final_weight' => 'integer',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacherSubject()
    {
        return $this->belongsTo(TeacherSubject::class);
    }
}
