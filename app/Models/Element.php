<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_dimention',
        'code',
        'description',
    ];

    public function dimention()
    {
        return $this->belongsTo(Dimention::class, 'code_dimention', 'code');
    }

    public function subElement()
    {
        return $this->hasMany(SubElement::class, 'code_element', 'code');
    }

    public function target()
    {
        return $this->hasMany(Target::class, 'code_element', 'code');
    }

    public function value()
    {
        return $this->hasMany(Value::class, 'code_element', 'code');
    }

    public function subValue()
    {
        return $this->hasMany(SubValue::class, 'code_element', 'code');
    }
}
