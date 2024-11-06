<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimention extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
    ];

    public function element()
    {
        return $this->hasMany(Element::class, 'code_dimention', 'code');
    }

    public function subElement()
    {
        return $this->hasMany(SubElement::class, 'code_dimention', 'code');
    }

    public function target()
    {
        return $this->hasMany(Target::class, 'code_dimention', 'code');
    }
}
