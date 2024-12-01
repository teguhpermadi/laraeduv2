<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubElement extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

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
