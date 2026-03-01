<?php

namespace App\Policies;

use App\Enums\AdminstrationLevel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if (Gate::denies('user_access')) {
            return false;
        }

        if ($user->cannot($ability)){
            return false;
        }
    }

    /**
     * check if updated user has higher level than updater
     *
     * @param User $user
     * @param AdminstrationLevel $level
     * @return bool
     */
    protected function hasAdministrationAccess(User $user,AdminstrationLevel $level): bool
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
        return $this->hasAdministrationAccess($user, $targetUser->administrator_level);
    }

    public function user_delete(User $user,User $targetUser)
    {
        return $this->hasAdministrationAccess($user,  $targetUser->administrator_level);
    }

    public function user_show(User $user,User $targetUser)
    {
        return $this->hasAdministrationAccess($user,$targetUser->administrator_level);
    }

    public function user_permission_edit(User $user,User $targetUser)
    {
        //Only super admin can change self permission.
        if ($targetUser->id == $user->id){
            if ($user->administrator_level != AdminstrationLevel::SUPER_ADMIN) {
                return false;
            }
        }

        return $this->hasAdministrationAccess($user,$targetUser->administrator_level);
    }

}
