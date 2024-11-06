<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_dimention',
        'code_element',
        'code',
        'description',
    ]; 

    public function value()
    {
        return $this->belongsTo(Value::class, 'code_element', 'code_element');
    }
}
