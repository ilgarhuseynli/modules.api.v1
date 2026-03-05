<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    public $table = 'customer_addresses';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'customer_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function format()
    {
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
