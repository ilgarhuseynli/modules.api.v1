<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Rental\Database\Factories\BadgeFactory;

class Badge extends Model
{
    use HasFactory;

    protected static function newFactory(): BadgeFactory
    {
        return BadgeFactory::new();
    }

    protected $table = 'rental_badges';

    protected $fillable = [
        'color',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static $sortable = [
        'id',
        'sort_order',
        'created_at',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(BadgeTranslation::class, 'badge_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'rental_car_badge', 'badge_id', 'car_id');
    }
}
