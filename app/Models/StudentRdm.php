<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class StudentRdm extends Model
{
    use HasUlids;

    protected $fillable = [
        'rdm_id',
        'nis',
        'nisn',
        'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class)->withoutGlobalScope(\App\Models\Scopes\StudentActiveScope::class);
    }
}
