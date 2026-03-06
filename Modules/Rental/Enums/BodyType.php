<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum BodyType: int
{
    case SEDAN = 1;
    case SUV = 2;
    case HATCHBACK = 3;
    case COUPE = 4;
    case VAN = 5;
    case WAGON = 6;
    case CONVERTIBLE = 7;
    case PICKUP = 8;

    public function data(): array
    {
        return match ($this) {
            self::SEDAN => [
                'id' => $this->value,
                'key' => 'sedan',
                'label' => 'SEDAN',
                'color' => 'blue',
                'icon' => 'car',
            ],
            self::SUV => [
                'id' => $this->value,
                'key' => 'suv',
                'label' => 'SUV',
                'color' => 'green',
                'icon' => 'car',
            ],
            self::HATCHBACK => [
                'id' => $this->value,
                'key' => 'hatchback',
                'label' => 'HATCHBACK',
                'color' => 'purple',
                'icon' => 'car',
            ],
            self::COUPE => [
                'id' => $this->value,
                'key' => 'coupe',
                'label' => 'COUPE',
                'color' => 'red',
                'icon' => 'car',
            ],
            self::VAN => [
                'id' => $this->value,
                'key' => 'van',
                'label' => 'VAN',
                'color' => 'gray',
                'icon' => 'truck',
            ],
            self::WAGON => [
                'id' => $this->value,
                'key' => 'wagon',
                'label' => 'WAGON',
                'color' => 'brown',
                'icon' => 'car',
            ],
            self::CONVERTIBLE => [
                'id' => $this->value,
                'key' => 'convertible',
                'label' => 'CONVERTIBLE',
                'color' => 'yellow',
                'icon' => 'car',
            ],
            self::PICKUP => [
                'id' => $this->value,
                'key' => 'pickup',
                'label' => 'PICKUP',
                'color' => 'orange',
                'icon' => 'truck',
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
