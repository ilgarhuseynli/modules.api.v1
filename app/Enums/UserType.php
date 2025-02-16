<?php

namespace App\Enums;

enum UserType: int
{
    case USER = 1;
    case CUSTOMER = 2;

    public static function getList(): array
    {
        return [
            self::USER->value => 'User',
            self::CUSTOMER->value => 'Customer',
        ];
    }

    public static function getName(int $value): string
    {
        return match ($value) {
            self::USER->value => 'User',
            self::CUSTOMER->value => 'Customer',
            default => throw new \ValueError("Invalid user type value: {$value}")
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
} 