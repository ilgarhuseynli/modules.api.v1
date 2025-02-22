<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $allPermissions = Permission::all();

        Role::findOrFail(Role::ROLE_ADMIN)
            ->permissions()
            ->sync($allPermissions->pluck('id'));



        $user_permissions = $allPermissions->reject(fn($permission) =>
            str_starts_with($permission->title, 'user_') ||
            str_starts_with($permission->title, 'role_') ||
            str_starts_with($permission->title, 'permission_')
        );

        Role::findOrFail(Role::ROLE_MODERATOR)
            ->permissions()
            ->sync($user_permissions->pluck('id'));
    }
}
