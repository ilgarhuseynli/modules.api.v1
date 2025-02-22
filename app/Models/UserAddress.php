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
        'zip',
        'city',
        'note',
        'is_billing_address',
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
            $this->street,
            $this->unit,
            $this->city,
            $this->state,
            $this->zip,
        ])->filter()->implode(', ');
    }

}
