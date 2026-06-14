<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AcademicYear extends Model
{
    use HasFactory;
    use HasUlids;
    use LogsActivity;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'year',
        'semester',
        'teacher_id',
        'date_report_half',
        'date_report',
        'date_graduation',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Academic Year')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}")
            ->logOnly(['*'])
            ->logExcept(['created_at', 'updated_at']);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('year', 'desc')->orderBy('semester', 'desc');
        });
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function legerWeight()
    {
        return $this->hasOne(LegerWeight::class, 'academic_year_id')
            ->whereNull('teacher_subject_id');
    }
}
