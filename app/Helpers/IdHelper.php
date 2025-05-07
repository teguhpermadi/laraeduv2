<?php

namespace App\Helpers;

class IdHelper
{
    /**
     * Generate a deterministic ULID-like string based on input.
     *
     * @param  string  $input
     * @return string
     */
    public static function deterministicUlidLike(string $input): string
    {
        // Hasil md5 adalah hex (base16), kita ubah ke base32 agar mirip ULID
        $hash = hash('sha256', $input); // Lebih kuat dari md5
        $base32 = static::toBase32($hash);

        return strtoupper(substr($base32, 0, 26)); // 26 karakter seperti ULID
    }

    /**
     * Convert a hex string (base16) to a base32 string (Crockford).
     *
     * @param  string  $hex
     * @return string
     */
    protected static function toBase32(string $hex): string
    {
        // Crockford's Base32 alphabet, mirip ULID
        $alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

        $binary = '';
        foreach (str_split($hex) as $char) {
            $binary .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }

        $base32 = '';
        foreach (str_split($binary, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $index = bindec($chunk);
            $base32 .= $alphabet[$index % 32];
        }

        return $base32;
    }
}
