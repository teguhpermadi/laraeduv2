<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Target extends Model
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
        'code_sub_element',
        'code',
        'phase',
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

    public function subElement()
    {
        return $this->belongsTo(SubElement::class, 'code_sub_element', 'code');
    }

    public function scopePhase(Builder $query, string $phase = null)
    {
        $query->where('phase', $phase);
    }
}
