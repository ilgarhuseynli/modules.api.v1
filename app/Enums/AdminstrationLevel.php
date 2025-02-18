<?php

namespace App\Enums;

enum AdminstrationLevel: int
{
    case SUPER_ADMIN = 1;
    case ADMIN = 2;
    case MODERATOR = 3;

    public static function getList(): array
    {
        return [
            self::SUPER_ADMIN->value => 'Super Admin',
            self::ADMIN->value => 'Admin',
            self::MODERATOR->value => 'Moderator',
        ];
    }

    public static function getName(int $value): string
    {
        return match ($value) {
            self::SUPER_ADMIN->value => 'Super Admin',
            self::ADMIN->value => 'Admin',
            self::MODERATOR->value => 'Moderator',
            default => throw new \ValueError("Invalid value: {$value}")
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
