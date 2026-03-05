<?php

namespace App\Models;

use App\Classes\Helpers;
use App\Enums\UserGender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'keyword',
        'is_company',
        'send_notification',
        'avatar_id',
        'gender',
        'birth_date',
        'address',
        'phones',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'address' => 'array',
            'phones' => 'array',
            'password' => 'hashed',
            'is_company' => 'boolean',
            'send_notification' => 'boolean',
            'gender' => UserGender::class,
        ];
    }

    public static $sortable = [
        'id',
        'name',
        'first_name',
        'last_name',
        'phone',
        'email',
        'created_at',
    ];

    public function getImageSizes(): array
    {
        return [
            'medium' => [600, 600],
            'small' => [300, 300],
            'thumbnail' => [100, 100],
        ];
    }

    // Relationships

    public function avatar()
    {
        return $this->morphOne(File::class, 'model')
            ->where('type', 'avatar')
            ->where('id', $this->avatar_id)
            ->latest();
    }

    public function files()
    {
        return $this->morphMany(File::class, 'model')->where('type', 'files');
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }

    // Attributes

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? ucfirst($value) : $value,
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Helpers::filterPhone($value)
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

        static::saving(function ($customer) {
            $customer->name = implode(' ', array_filter([
                $customer->first_name,
                $customer->last_name,
            ]));
            $customer->keyword = implode(' ', array_filter([
                $customer->first_name,
                $customer->last_name,
                $customer->phone,
                $customer->email,
            ]));
        });
    }

    // Query Scopes

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(filled($filters['name'] ?? null), fn ($q) => $q->where('name', 'like', '%'.$filters['name'].'%'))
            ->when(filled($filters['keyword'] ?? null), fn ($q) => $q->where('keyword', 'like', '%'.$filters['keyword'].'%'))
            ->when(filled($filters['phone'] ?? null), fn ($q) => $q->where('phone', $filters['phone']));
    }
}
