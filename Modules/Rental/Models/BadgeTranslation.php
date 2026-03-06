<?php

namespace Modules\Rental\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeTranslation extends Model
{
    protected $table = 'rental_badge_translations';

    public $timestamps = false;

    protected $fillable = [
        'badge_id',
        'locale',
        'name',
    ];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }
}
