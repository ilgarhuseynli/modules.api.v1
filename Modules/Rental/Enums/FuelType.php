<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum FuelType: int
{
    case PETROL = 1;
    case DIESEL = 2;
    case HYBRID = 3;
    case ELECTRIC = 4;

    public function data(): array
    {
        return match ($this) {
            self::PETROL => [
                'id' => $this->value,
                'key' => 'petrol',
                'label' => 'PETROL',
                'color' => 'orange',
                'icon' => 'fuel',
            ],
            self::DIESEL => [
                'id' => $this->value,
                'key' => 'diesel',
                'label' => 'DIESEL',
                'color' => 'gray',
                'icon' => 'fuel',
            ],
            self::HYBRID => [
                'id' => $this->value,
                'key' => 'hybrid',
                'label' => 'HYBRID',
                'color' => 'green',
                'icon' => 'leaf',
            ],
            self::ELECTRIC => [
                'id' => $this->value,
                'key' => 'electric',
                'label' => 'ELECTRIC',
                'color' => 'blue',
                'icon' => 'zap',
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
