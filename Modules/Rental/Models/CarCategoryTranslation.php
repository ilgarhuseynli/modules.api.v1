<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarCategoryTranslation extends Model
{
    protected $table = 'rental_car_category_translations';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'slug',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'category_id');
    }
}
