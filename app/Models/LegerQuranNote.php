<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerQuranNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function legerQuran()
    {
        return $this->belongsTo(LegerQuran::class);
    }
}
