<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LinkertScaleEnum: String implements HasLabel
{
    // ubah menjadi amat baik, baik, cukup, kurang, amat kurang
    // jika amat baik score 4, baik score 3, cukup score 2, kurang score 1, amat kurang score 0
    case AMAT_BAIK = '4';
    case BAIK = '3';
    case CUKUP = '2';
    case KURANG = '1';
    case AMAT_KURANG = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::AMAT_BAIK => 'Amat Baik',
            self::BAIK => 'Baik',
            self::CUKUP => 'Cukup',
            self::KURANG => 'Kurang',
            self::AMAT_KURANG => 'Amat Kurang',
        };
    }   
}
