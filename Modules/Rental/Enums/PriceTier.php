<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum PriceTier: int
{
    case DAILY = 1;
    case WEEKLY = 2;
    case MONTHLY = 3;

    public function data(): array
    {
        return match ($this) {
            self::DAILY => [
                'id' => $this->value,
                'key' => 'daily',
                'label' => 'DAILY',
                'color' => 'blue',
                'icon' => 'calendar',
            ],
            self::WEEKLY => [
                'id' => $this->value,
                'key' => 'weekly',
                'label' => 'WEEKLY',
                'color' => 'green',
                'icon' => 'calendar',
            ],
            self::MONTHLY => [
                'id' => $this->value,
                'key' => 'monthly',
                'label' => 'MONTHLY',
                'color' => 'purple',
                'icon' => 'calendar',
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
