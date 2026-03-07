<?php

namespace Modules\Rental\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Rental\Enums\BookingStatus;
use Modules\Rental\Enums\PaymentStatus;
use Modules\Rental\Enums\PriceTier;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rental_bookings';

    protected $fillable = [
        'car_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'pickup_location_id',
        'dropoff_location_id',
        'pickup_date',
        'dropoff_date',
        'days',
        'price_tier',
        'price_per_day',
        'base_price',
        'extras_total',
        'locations_total',
        'discount',
        'total_price',
        'deposit',
        'note',
        'coupon_code',
        'status',
        'payment_status',
        'paid_amount',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'price_tier' => PriceTier::class,
            'pickup_date' => 'datetime',
            'dropoff_date' => 'datetime',
            'days' => 'integer',
            'price_per_day' => 'decimal:2',
            'base_price' => 'decimal:2',
            'extras_total' => 'decimal:2',
            'locations_total' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'deposit' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public static $sortable = [
        'id',
        'pickup_date',
        'dropoff_date',
        'total_price',
        'status',
        'created_at',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    public function extras(): HasMany
    {
        return $this->hasMany(BookingExtra::class, 'booking_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['status'] ?? null), fn ($q) => $q->where('status', $filters['status']))
            ->when(filled($filters['payment_status'] ?? null), fn ($q) => $q->where('payment_status', $filters['payment_status']))
            ->when(filled($filters['car_id'] ?? null), fn ($q) => $q->where('car_id', $filters['car_id']))
            ->when(filled($filters['customer_id'] ?? null), fn ($q) => $q->where('customer_id', $filters['customer_id']))
            ->when(filled($filters['date_from'] ?? null), fn ($q) => $q->where('pickup_date', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn ($q) => $q->where('dropoff_date', '<=', $filters['date_to']));
    }
}
