<?php

namespace Modules\Rental\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Rental\Database\Factories\CarFactory;
use Modules\Rental\Enums\BodyType;
use Modules\Rental\Enums\FuelType;
use Modules\Rental\Enums\Transmission;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): CarFactory
    {
        return CarFactory::new();
    }

    protected $table = 'rental_cars';

    protected $fillable = [
        'category_id',
        'brand',
        'model',
        'year',
        'plate_number',
        'color',
        'transmission',
        'fuel_type',
        'body_type',
        'seats',
        'doors',
        'engine',
        'price_daily',
        'price_weekly',
        'price_monthly',
        'deposit',
        'is_active',
        'sort_order',
        'avatar_id',
    ];

    protected function casts(): array
    {
        return [
            'transmission' => Transmission::class,
            'fuel_type' => FuelType::class,
            'body_type' => BodyType::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'year' => 'integer',
            'seats' => 'integer',
            'doors' => 'integer',
            'price_daily' => 'decimal:2',
            'price_weekly' => 'decimal:2',
            'price_monthly' => 'decimal:2',
            'deposit' => 'decimal:2',
        ];
    }

    public static $sortable = [
        'id',
        'brand',
        'model',
        'year',
        'price_daily',
        'sort_order',
        'created_at',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(CarTranslation::class, 'car_id');
    }

    public function translation(?string $locale = null): HasMany
    {
        return $this->translations()->where('locale', $locale ?? app()->getLocale());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'category_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'rental_car_badge', 'car_id', 'badge_id');
    }

    public function extras(): BelongsToMany
    {
        return $this->belongsToMany(Extra::class, 'rental_car_extra', 'car_id', 'extra_id')
            ->withPivot(['price', 'price_type']);
    }

    public function avatar(): MorphOne
    {
        return $this->morphOne(File::class, 'model')
            ->where('type', 'avatar')
            ->where('id', $this->avatar_id)
            ->latest();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(File::class, 'model')->where('type', 'images');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'car_id');
    }

    public function getImageSizes(): array
    {
        return [
            'medium' => [600, 600],
            'small' => [300, 300],
            'thumbnail' => [100, 100],
        ];
    }

    public function getPriceForDays(int $days): string
    {
        if ($days >= 30 && $this->price_monthly) {
            return $this->price_monthly;
        }

        if ($days >= 7 && $this->price_weekly) {
            return $this->price_weekly;
        }

        return $this->price_daily;
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['brand'] ?? null), fn ($q) => $q->where('brand', 'like', '%'.$filters['brand'].'%'))
            ->when(filled($filters['model'] ?? null), fn ($q) => $q->where('model', 'like', '%'.$filters['model'].'%'))
            ->when(filled($filters['category_id'] ?? null), fn ($q) => $q->where('category_id', $filters['category_id']))
            ->when(filled($filters['transmission'] ?? null), fn ($q) => $q->where('transmission', $filters['transmission']))
            ->when(filled($filters['fuel_type'] ?? null), fn ($q) => $q->where('fuel_type', $filters['fuel_type']))
            ->when(filled($filters['body_type'] ?? null), fn ($q) => $q->where('body_type', $filters['body_type']))
            ->when(filled($filters['is_active'] ?? null), fn ($q) => $q->where('is_active', $filters['is_active']));
    }
}
