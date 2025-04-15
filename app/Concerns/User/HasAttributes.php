<?php

namespace App\Concerns\User;

use App\Classes\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasAttributes
{
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? ucfirst($value) : $value,
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Helpers::filterPhone($value) // Keep only numbers
        );
    }

    protected function phones(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => collect($value)->map(fn ($phone) => Helpers::filterPhone($phone))->toJson()
        );
    }


    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            $user->name = implode(' ', array_filter([
                $user->first_name,
                $user->last_name
            ]));
            $user->keyword = implode(' ', array_filter([
                $user->first_name,
                $user->last_name,
                $user->phone,
                $user->email
            ]));
        });
    }


}
