<?php

namespace App\Enums;

enum UserGender: int
{
    case MALE = 1;
    case FEMALE = 2;
    case OTHER = 3;

    public static function getList(): array
    {
        return [
            self::MALE->value => 'Male',
            self::FEMALE->value => 'Female',
            self::OTHER->value => 'Other',
        ];
    }

    public static function getName(int $value): string
    {
        return match ($value) {
            self::MALE->value => 'Male',
            self::FEMALE->value => 'Female',
            self::OTHER->value => 'Other',
            default => throw new \ValueError("Invalid gender value: {$value}")
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
} 