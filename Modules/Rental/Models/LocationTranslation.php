<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationTranslation extends Model
{
    protected $table = 'rental_location_translations';

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'locale',
        'name',
        'address',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
