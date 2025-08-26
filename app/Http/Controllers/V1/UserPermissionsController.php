<?php

namespace App\Http\Controllers\V1;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UserPermissionsController extends Controller
{

    public function index(Request $request,User $user)
    {
        Gate::authorize('user_permission_edit');

        $permList = $user->getPermissions();
        $responseType = $request->input('response_type');

        $res = [];
        foreach ($permList as $perm) {

            $titles = explode('_', $perm['title']);
            $group = $titles[0];

            $perm['label'] = implode(' ', $titles);
            $perm['allow'] = !!$perm['allow'];

            $res[$group][] = $perm;
        }

        if ($responseType === 'blade') {
            $bladeRes = '';

            foreach ($res as $group => $permissions) {
                $bladeRes .= view('admin.users.edit.partials.permission-group', compact('permissions', 'group'))->render();
            }

            return response()->json([
                'html' => $bladeRes,
            ]);
        }

        return response()->json($res);

    }

    public function update(Request $request,User $user)
    {
        Gate::authorize('user_permission_edit');

        $permissionId = $request->permission_id;
        $allowed = (bool)$request->allow;

        // Check if the permission exists directly in the database
        $permExist = Permission::where('id', $permissionId)->exists();

        if (!$permExist) {
            return response()->json('PermissionNotExists',Response::HTTP_NOT_FOUND);
        }

        if ($request->locked) {
            if ($allowed){
                $user->addCustomPermission($permissionId);

                if (array_key_exists('all',$request->toArray())){
                    $user->setAllAccess($permissionId,$request->all);
                }
            }else{
                $user->removeCustomPermission($permissionId);
            }
        } else {
            $user->restorePermission($permissionId);
        }

        $authGateKey = 'auth_user_permissions_'.$user->id;
        Cache::forget($authGateKey);

        return response()->json('Updated successfully');
    }

}
