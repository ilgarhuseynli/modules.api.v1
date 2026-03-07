<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Rental\Database\Factories\LocationFactory;

class Location extends Model
{
    use HasFactory;

    protected static function newFactory(): LocationFactory
    {
        return LocationFactory::new();
    }

    protected $table = 'rental_locations';

    protected $fillable = [
        'is_active',
        'price',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
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
        return $this->hasMany(LocationTranslation::class, 'location_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }
}
