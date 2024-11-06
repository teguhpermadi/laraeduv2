<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_dimention',
        'code_element',
        'code',
        'description',
    ];

    public function dimention()
    {
        return $this->belongsTo(Dimention::class, 'code_dimention', 'code');
    }

    public function element()
    {
        return $this->belongsTo(Element::class, 'code_element', 'code');
    }

    public function target()
    {
        return $this->hasOne(Target::class, 'code_sub_element', 'code');
    }
}
