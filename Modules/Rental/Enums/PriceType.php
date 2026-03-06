<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum PriceType: int
{
    case FLAT = 1;
    case PER_DAY = 2;

    public function data(): array
    {
        return match ($this) {
            self::FLAT => [
                'id' => $this->value,
                'key' => 'flat',
                'label' => 'FLAT',
                'color' => 'blue',
                'icon' => 'dollar-sign',
            ],
            self::PER_DAY => [
                'id' => $this->value,
                'key' => 'per_day',
                'label' => 'PER_DAY',
                'color' => 'green',
                'icon' => 'repeat',
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
