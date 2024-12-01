<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegerNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function leger()
    {
        return $this->belongsTo(Leger::class);
    }
}
