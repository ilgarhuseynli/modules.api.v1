<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Permission extends Model
{

    public $table = 'permissions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }


    public static function check($permission){
        if (!Auth::id()){
            return false;
        }

        $permissions = Auth::user()->getAssignedPermissions();

        foreach ($permissions as $permissionData){
            if ($permissionData['title'] == $permission){
                return $permissionData['allow'];
            }
        }

        return false;
    }


}
