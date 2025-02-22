<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\User\HasAttributes;
use App\Concerns\User\HasQueryScopes;
use App\Concerns\User\HasRelations;
use App\Enums\AdminstrationLevel;
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

    protected array $defaultImageSizes = [
        'medium' => [600, 600],
        'small' => [300, 300],
        'thumbnail' => [100, 100]
    ];









    //PERMISSION FUNCTIONS

    public function addCustomPermission($permissionId)
    {
        $this->customPermissions()->syncWithoutDetaching([
            $permissionId => ['allow' => true]
        ]);
    }

    public function removeCustomPermission($permissionId)
    {
        $this->customPermissions()->syncWithoutDetaching([
            $permissionId => ['allow' => false]
        ]);
    }

    public function restorePermission($permissionId)
    {
        $this->customPermissions()->detach($permissionId);
    }


    // Grant or remove "all" access to a permission
    public function setAllAccess($permissionId, $enableAll = true)
    {
        $this->customPermissions()->syncWithoutDetaching([
            $permissionId => ['all' => $enableAll, 'allow' => true]
        ]);
    }


    public function hasPermission($permission, $checkAll = false)
    {
        // Check if the permission is not allow
        if ($this->customPermissions()->where('title', $permission)->wherePivot('allow', false)->exists()) {
            return false;
        }

        // Check if user has custom permission with "all" set to true
        if ($checkAll && $this->customPermissions()->where('title', $permission)->wherePivot('all', true)->exists()) {
            return true;
        }

        // Check for custom permissions without negation
        if ($this->customPermissions()->where('title', $permission)->wherePivot('allow', true)->exists()) {
            return true;
        }

        // Fall back to role permissions if no custom permission is found
        return $this->role && $this->role->permissions->contains('title', $permission);
    }


    public function getPermissions(){

        $allPermissions = Permission::all()->pluck('title', 'id');

        $rolePermissions = $this->role->permissions()->pluck('title', 'id');

        $userPermissions = $this->customPermissions()->withPivot(['allow', 'all'])->get()->pluck('pivot', 'id');

        $res = [];
        foreach ($allPermissions as $id => $title){
            $currPerm = [
                'id' => $id,
                'label' => $title,
                'title' => $title,
                'allow' => 0,
                'locked' => 0,
            ];

            $customUserPerm = @$userPermissions[$id];

            if ($customUserPerm){
                $currPerm['allow'] = (int)$customUserPerm['allow'];
//                $currPerm['all'] = $customUserPerm['all'];
                $currPerm['locked'] = 1;
            }else if (@$rolePermissions[$id]){
                $currPerm['allow'] = 1;
            }

            $res[] = $currPerm;
        }

        return $res;
    }


    public function getAssignedPermissions()
    {
        $rolePermissions = $this->role->permissions()->pluck('title', 'id');

        $userPermissions = $this->customPermissions()->withPivot(['allow', 'all'])->get();

        $res = [];
        foreach ($rolePermissions as $id => $title){
            $res[$id] = [
                'id' => $id,
                'title' => $title,
                'allow' => 1,
            ];
        }

        foreach ($userPermissions as $data){
            $res[$data['id']] = [
                'id' => $data['id'],
                'title' => $data->title,
                'allow' => (int)$data->pivot->allow,
            ];
        }

        return $res;
    }


}
