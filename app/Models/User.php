<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\User\HasAttributes;
use App\Concerns\User\HasQueryScopes;
use App\Concerns\User\HasRelations;
use App\Enums\AdminstrationLevel;
use App\Traits\HasPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserGender;
use App\Enums\UserType;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,
        Notifiable,
        TwoFactorAuthenticatable,
        HasPermission,
        HasApiTokens,
        HasRelations,
        HasQueryScopes,
        HasAttributes;

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
        'administrator_level',
        'send_notification',
        'type', //user | customer
        'role_id',
        'avatar_id',
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
            'is_company' => 'boolean',
            'send_notification' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'two_factor_verified_at' => 'datetime',
            'type' => UserType::class,
            'gender' => UserGender::class,
            'administrator_level' => AdminstrationLevel::class,
        ];
    }


    public static $sortable = [
        "id",
        'name',
        "first_name",
        "last_name",
        "phone",
        'email',
        'created_at',
    ];



    public function getImageSizes() : array
    {
        return [
            'medium' => [600, 600],
            'small' => [300, 300],
            'thumbnail' => [100, 100]
        ];
    }


}
