<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDescription extends Model
{
    use HasFactory;
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'academic_year_id',
        'knowledge_template',
        'skill_template',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
