<?php

namespace App\Concerns\User;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasAttributes
{
    protected function keyword(): Attribute
    {
        return Attribute::make(
            set: fn () => implode(' ', array_filter([
                $this->first_name,
                $this->last_name,
                $this->phone,
                $this->email
            ]))
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn () => implode(' ', array_filter([
                $this->first_name,
                $this->last_name
            ]))
        );
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }

}
