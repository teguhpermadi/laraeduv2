<?php

namespace App\Enums;

enum CategoryLegerEnum: string
{
    case FULL_SEMESTER = 'full_semester';
    case HALF_SEMESTER = 'half_semester';

    public function getLabel(): string 
    {
        return match($this) {
            self::FULL_SEMESTER => 'Akhir Semester',
            self::HALF_SEMESTER => 'Tengah Semester',
        };
    }

    public static function toArray(): array
    {
        return array_map(function($item) {
            return [
                'value' => $item->value,
                'label' => $item->getLabel(),
            ];
        }, self::cases());
    }

    public static function fromValue(string $value): ?self
    {
        return match($value) {
            'full_semester' => self::FULL_SEMESTER,
            'half_semester' => self::HALF_SEMESTER,
            default => null
        };
    }
}
