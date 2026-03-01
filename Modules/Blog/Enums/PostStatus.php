<?php

declare(strict_types=1);

namespace Modules\Blog\Enums;

enum PostStatus: int
{
    case DRAFT = 1;
    case PUBLISHED = 2;
    case ARCHIVED = 3;

    public function data(): array
    {
        return match ($this) {
            self::DRAFT => [
                'id' => $this->value,
                'key' => 'draft',
                'label' => 'DRAFT',
                'color' => 'gray',
                'icon' => 'pencil',
            ],
            self::PUBLISHED => [
                'id' => $this->value,
                'key' => 'published',
                'label' => 'PUBLISHED',
                'color' => 'green',
                'icon' => 'check',
            ],
            self::ARCHIVED => [
                'id' => $this->value,
                'key' => 'archived',
                'label' => 'ARCHIVED',
                'color' => 'red',
                'icon' => 'archive',
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
