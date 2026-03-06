<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarTranslation extends Model
{
    protected $table = 'rental_car_translations';

    public $timestamps = false;

    protected $fillable = [
        'car_id',
        'locale',
        'description',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
