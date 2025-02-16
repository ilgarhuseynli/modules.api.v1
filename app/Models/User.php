<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserGender;
use App\Enums\UserType;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'keyword',
        'is_company',
        'send_notification',
        'type', //user | customer
        'avatar',
        'gender',
        'birth_date',
        'address', //json obj
        'phones', //json list
        'password'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_trusted_devices'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'address' => 'array',
            'phones' => 'array',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_verified_at' => 'datetime',
            'type' => UserType::class,
            'gender' => UserGender::class,
        ];
    }

    public function activeDevices(): HasMany
    {
        return $this->hasMany(ActiveDevice::class);
    }
}
