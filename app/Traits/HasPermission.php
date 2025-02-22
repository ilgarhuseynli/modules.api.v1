<?php

namespace App\Traits;

use App\Models\Permission;

trait HasPermission
{



    //PERMISSION FUNCTIONS

    public function addCustomPermission($permissionId)
    {
        $this->permissions()->syncWithoutDetaching([
            $permissionId => ['allow' => true]
        ]);
    }

    public function removeCustomPermission($permissionId)
    {
        $this->permissions()->syncWithoutDetaching([
            $permissionId => ['allow' => false]
        ]);
    }

    public function restorePermission($permissionId)
    {
        $this->permissions()->detach($permissionId);
    }


    // Grant or remove "all" access to a permission
    public function setAllAccess($permissionId, $enableAll = true)
    {
        $this->permissions()->syncWithoutDetaching([
            $permissionId => ['all' => $enableAll, 'allow' => true]
        ]);
    }


    public function hasPermission($permission, $checkAll = false)
    {
        // Check if the permission is not allow
        if ($this->permissions()->where('title', $permission)->wherePivot('allow', false)->exists()) {
            return false;
        }

        // Check if user has custom permission with "all" set to true
        if ($checkAll && $this->permissions()->where('title', $permission)->wherePivot('all', true)->exists()) {
            return true;
        }

        // Check for custom permissions without negation
        if ($this->permissions()->where('title', $permission)->wherePivot('allow', true)->exists()) {
            return true;
        }

        // Fall back to role permissions if no custom permission is found
        return $this->role && $this->role->permissions->contains('title', $permission);
    }


    public function getPermissions(){

        $allPermissions = Permission::all()->pluck('title', 'id');

        $rolePermissions = $this->role->permissions()->pluck('title', 'id');

        $userPermissions = $this->permissions()->withPivot(['allow', 'all'])->get()->pluck('pivot', 'id');

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

        $userPermissions = $this->permissions()->withPivot(['allow', 'all'])->get();

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
