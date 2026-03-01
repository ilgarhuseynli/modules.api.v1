<?php

namespace Modules\Blog\Enums;

enum PostStatus: int
{
    case DRAFT = 1;
    case PUBLISHED = 2;
    case ARCHIVED = 3;

    public static function getList(): array
    {
        return [
            self::DRAFT->value => 'Draft',
            self::PUBLISHED->value => 'Published',
            self::ARCHIVED->value => 'Archived',
        ];
    }

    public static function getName(int $value): string
    {
        return match ($value) {
            self::DRAFT->value => 'Draft',
            self::PUBLISHED->value => 'Published',
            self::ARCHIVED->value => 'Archived',
            default => throw new \ValueError("Invalid post status value: {$value}")
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function data(): array
    {
        return [
            'key' => $this->key(),
            'value' => $this->value,
            'label' => $this->label(),
            'meta' => $this->meta(),
        ];
    }

    public function key(): string
    {
        return match ($this) {
            self::DRAFT => 'draft',
            self::PUBLISHED => 'published',
            self::ARCHIVED => 'archived',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
        };
    }

    public function meta(): array
    {
        return match ($this) {
            self::DRAFT => ['color' => 'gray', 'icon' => 'pencil'],
            self::PUBLISHED => ['color' => 'green', 'icon' => 'check'],
            self::ARCHIVED => ['color' => 'red', 'icon' => 'archive'],
        };
    }
}
