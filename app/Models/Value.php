<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_element',
        'code_dimention',
        'code',
        'description',
    ];

    public function subValue()
    {
        return $this->hasMany(SubValue::class, 'code_element', 'code_element');
    }
}
