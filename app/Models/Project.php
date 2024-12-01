<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'academic_year_id',
        'grade_id',
        'teacher_id',
        'project_theme_id',
        'name',
        'description',
        'phase',
    ];

    public function academic()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function teacher()
    {
         return $this->belongsTo(Teacher::class);
    }
    
    public function grade()
    {
         return $this->belongsTo(Grade::class)->with(['studentGrade' => function ($query) {
            $query->withoutGlobalScope(AcademicYearScope::class);
         }]);
    }

    public function projectTarget()
    {
        return $this->hasMany(ProjectTarget::class);
    }

    public function note()
    {
        return $this->hasMany(ProjectNote::class);
    }

    public function scopeMyProject(Builder $query, $teacher_id = null):void
    {
        if(is_null($teacher_id)){
            $teacher_id = auth()->user()->userable->userable_id;
        }

        $query->where('teacher_id', $teacher_id);
    }

    public function projectTheme()
    {
        return $this->belongsTo(ProjectTheme::class);
    }
}
