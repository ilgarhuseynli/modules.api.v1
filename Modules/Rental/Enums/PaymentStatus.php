<?php

declare(strict_types=1);

namespace Modules\Rental\Enums;

enum PaymentStatus: int
{
    case UNPAID = 1;
    case PARTIAL = 2;
    case PAID = 3;

    public function data(): array
    {
        return match ($this) {
            self::UNPAID => [
                'id' => $this->value,
                'key' => 'unpaid',
                'label' => 'UNPAID',
                'color' => 'red',
                'icon' => 'alert-circle',
            ],
            self::PARTIAL => [
                'id' => $this->value,
                'key' => 'partial',
                'label' => 'PARTIAL',
                'color' => 'yellow',
                'icon' => 'minus-circle',
            ],
            self::PAID => [
                'id' => $this->value,
                'key' => 'paid',
                'label' => 'PAID',
                'color' => 'green',
                'icon' => 'check-circle',
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
