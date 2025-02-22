<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permssions = [
            "user_management_access",

            "user_create",
            "user_edit",
            "user_show",
            "user_delete",
            "user_access",

            "role_create",
            "role_edit",
            "role_show",
            "role_delete",
            "role_access",

            "permission_access",

            "service_delete",
            "service_access",
            "service_create",
            "service_edit",
            "service_show",

            "tag_access",
            "tag_create",
            "tag_edit",
            "tag_delete",

            "blog_access",
            "blog_create",
            "blog_edit",
            "blog_delete",
            "blog_show",

            "faq_access",
            "faq_create",
            "faq_edit",
            "faq_delete",
        ];

        foreach ($permssions as $title) {
            Permission::updateOrInsert(
                ['title' => $title],
                ['title' => $title]
            );
        }

    }
}
