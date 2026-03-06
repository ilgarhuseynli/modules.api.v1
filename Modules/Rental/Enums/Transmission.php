<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum Transmission: int
{
    case AUTOMATIC = 1;
    case MANUAL = 2;

    public function data(): array
    {
        return match ($this) {
            self::AUTOMATIC => [
                'id' => $this->value,
                'key' => 'automatic',
                'label' => 'AUTOMATIC',
                'color' => 'blue',
                'icon' => 'settings',
            ],
            self::MANUAL => [
                'id' => $this->value,
                'key' => 'manual',
                'label' => 'MANUAL',
                'color' => 'gray',
                'icon' => 'settings',
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
