<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraTranslation extends Model
{
    protected $table = 'rental_extra_translations';

    public $timestamps = false;

    protected $fillable = [
        'extra_id',
        'locale',
        'name',
        'description',
    ];

    public function extra(): BelongsTo
    {
        return $this->belongsTo(Extra::class, 'extra_id');
    }
}
