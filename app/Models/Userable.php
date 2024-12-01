<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userable extends Model
{
    use HasFactory;
    use HasUlids;

    // Menentukan bahwa kita tidak menggunakan auto-increment
    public $incrementing = false;

    // Tipe primary key adalah string (karena ULID berupa string)
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'userable_id',
        'userable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userable()
    {
        return $this->morphTo();
    }

    protected function userableType(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => class_basename($value),
        );
    }
}
