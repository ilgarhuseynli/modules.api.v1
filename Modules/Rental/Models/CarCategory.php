<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Rental\Database\Factories\CarCategoryFactory;

class CarCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): CarCategoryFactory
    {
        return CarCategoryFactory::new();
    }

    protected $table = 'rental_car_categories';

    protected $fillable = [
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
        return $this->hasMany(CarCategoryTranslation::class, 'category_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'category_id');
    }
}
