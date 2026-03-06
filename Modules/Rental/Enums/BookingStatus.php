<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum BookingStatus: int
{
    case PENDING = 1;
    case CONFIRMED = 2;
    case ACTIVE = 3;
    case COMPLETED = 4;
    case CANCELLED = 5;

    public function data(): array
    {
        return match ($this) {
            self::PENDING => [
                'id' => $this->value,
                'key' => 'pending',
                'label' => 'PENDING',
                'color' => 'yellow',
                'icon' => 'clock',
            ],
            self::CONFIRMED => [
                'id' => $this->value,
                'key' => 'confirmed',
                'label' => 'CONFIRMED',
                'color' => 'blue',
                'icon' => 'check',
            ],
            self::ACTIVE => [
                'id' => $this->value,
                'key' => 'active',
                'label' => 'ACTIVE',
                'color' => 'green',
                'icon' => 'play',
            ],
            self::COMPLETED => [
                'id' => $this->value,
                'key' => 'completed',
                'label' => 'COMPLETED',
                'color' => 'gray',
                'icon' => 'check-circle',
            ],
            self::CANCELLED => [
                'id' => $this->value,
                'key' => 'cancelled',
                'label' => 'CANCELLED',
                'color' => 'red',
                'icon' => 'x-circle',
            ],
        };
    }

    public function key(): string
    {
        return (string) $this->data()['key'];
    }

    public function label(): string
    {
        return (string) $this->data()['label'];
    }

    public function meta(): array
    {
        $d = $this->data();

        return [
            'id' => $d['id'],
            'enum' => $d['key'],
            'value' => $d['key'],
            'label' => $d['label'],
            'color' => $d['color'],
            'icon' => $d['icon'],
        ];
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
