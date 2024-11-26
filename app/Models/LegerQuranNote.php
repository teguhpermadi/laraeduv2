<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerQuranNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'leger_quran_id',
        'note',
    ];

    public function legerQuran()
    {
        return $this->belongsTo(LegerQuran::class);
    }
}
