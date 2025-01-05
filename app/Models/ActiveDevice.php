<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveDevice extends Model
{
    protected $fillable = [
        'user_id',
        'token_id',
        'device_name',
        'device_type',
        'ip_address',
        'location',
        'last_active_at'
    ];

    protected $casts = [
        'last_active_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 