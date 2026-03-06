<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Rental\Enums\PriceType;

class BookingExtra extends Model
{
    protected $table = 'rental_booking_extras';

    protected $fillable = [
        'booking_id',
        'extra_id',
        'name',
        'price',
        'price_type',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'price_type' => PriceType::class,
            'price' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class, 'extra_id');
    }
}
