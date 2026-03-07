<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Rental\Database\Factories\ExtraFactory;
use Modules\Rental\Enums\PriceType;

class Extra extends Model
{
    use HasFactory;

    protected static function newFactory(): ExtraFactory
    {
        return ExtraFactory::new();
    }

    protected $table = 'rental_extras';

    protected $fillable = [
        'price',
        'price_type',
        'is_global',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_type' => PriceType::class,
            'is_global' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public static $sortable = [
        'id',
        'sort_order',
        'created_at',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ExtraTranslation::class, 'extra_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'rental_car_extra', 'extra_id', 'car_id')
            ->withPivot(['price', 'price_type']);
    }
}
