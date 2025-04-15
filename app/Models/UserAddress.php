<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    public $table = 'user_addresses';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'street',
        'unit',
        'state',
        'postal_code',
        'city',
        'country',
        'note',
        'is_primary',
        'created_at',
        'updated_at',
    ];

    //RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function format()
    {
        // Customize this format as needed
        return collect([
            $this->country,
            $this->city,
            $this->state,
            $this->street,
            $this->unit,
            $this->postal_code,
        ])->filter()->implode(', ');
    }

}
