<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->cannot($ability)){
            return false;
        }
    }

    protected function hasAdministrationAccess(User $user, $level): bool
    {
        $authUser = Auth::user();
        if ($level){
            if (!$authUser->administrator_level){
                return false;
            }

            if ($level < $authUser->administrator_level){
                return false;
            }
        }

        return true;
    }

    public function user_create(User $user, $level)
    {
        return $this->hasAdministrationAccess($user, $level);
    }

    public function user_edit(User $user,User $targetUser)
    {
        return $this->hasAdministrationAccess($user, $targetUser->role_id);
    }

    public function user_delete(User $user,User $targetUser)
    {
        return $this->hasAdministrationAccess($user,  $targetUser->role_id);
    }

    public function user_show($user,User $targetUser)
    {
        return $this->hasAdministrationAccess($user,$targetUser->role_id);
    }

}
