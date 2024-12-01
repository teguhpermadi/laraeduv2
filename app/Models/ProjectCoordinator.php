<?php

namespace App\Models;

use App\Models\Scopes\AcademicYearScope;
use App\Observers\ProjectCoordinatorObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ProjectCoordinatorObserver::class)]
class ProjectCoordinator extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'grade_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new AcademicYearScope);
    }

    public function teacher()
    {
         return $this->belongsTo(Teacher::class);
    }
    
    public function grade()
    {
         return $this->belongsTo(Grade::class);
    }
}
