<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthGates
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!app()->runningInConsole() && $user) {
            $key = 'auth_user_permissions_'.$user->id;

            $userPermissions = Cache::get($key);
            if(!$userPermissions){
                $userPermissions = $user->getAssignedPermissions();
                Cache::forget($key);
                Cache::add($key,$userPermissions,now()->addMinutes(10));
            }

            foreach ($userPermissions as $permission) {
                if ($permission['allow']){
                    Gate::define($permission['title'], function () {
                        return true;
                    });
                }
            }
        }

        return $next($request);
    }
}
