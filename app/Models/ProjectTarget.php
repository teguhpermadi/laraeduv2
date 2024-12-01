<?php

namespace App\Models;

use App\Observers\ProjectTargetObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// observer
#[ObservedBy([ProjectTargetObserver::class])]
class ProjectTarget extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function dimention()
    {
        return $this->belongsTo(Dimention::class);
    }

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
    
    public function subElement()
    {
        return $this->belongsTo(SubElement::class);
    }
    
    public function value()
    {
        return $this->belongsTo(Value::class);
    }

    public function subValue()
    {
        return $this->belongsTo(SubValue::class);
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    public function studentProject()
    {
        return $this->hasMany(StudentProject::class);
    }
}
