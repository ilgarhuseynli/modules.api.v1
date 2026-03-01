<?php

namespace Database\Seeders;

use App\Enums\AdminstrationLevel;
use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //        $this->call([
        //            PermissionsTableSeeder::class,
        //            PermissionRoleTableSeeder::class,
        //        ]);

        //        $this->call([
        //            PermissionsTableSeeder::class,
        //            RolesTableSeeder::class,
        //            PermissionRoleTableSeeder::class,
        //            SettingsTableSeeder::class,
        //        ]);
        //
        //        User::factory()->create([
        //            'email' => 'admin@admin.com',
        //            'type' => UserType::EMPLOYEE,
        //            'administrator_level' => AdminstrationLevel::SUPER_ADMIN,
        //            'role_id' => Role::ROLE_ADMIN
        //        ]);
        //
        //        User::factory(10)->create();

        //        \Illuminate\Support\Facades\Artisan::call('module:seed Blog');

        $this->call([
            \Modules\Blog\Database\Seeders\BlogSeeder::class,
        ]);
    }
}
