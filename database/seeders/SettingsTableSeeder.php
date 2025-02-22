<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'contact_phone',
                'value' => '(+994 55) 555-55-55'
            ],
            [
                'key' => 'contact_home_phone',
                'value' => '(+994 55) 555-55-55'
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@domain.com'
            ],
            [
                'key' => 'contact_location',
                'value' => 'Nərimanov rayonu Albert Aqarunov küçəsi 14a-22b'
            ],
            [
                'key' => 'work_hours',
                'value' => '09:00 - 18:00'
            ],
            [
                'key' => 'social_facebook',
                'value' => 'facebook.com'
            ],
            [
                'key' => 'social_twitter',
                'value' => 'twitter.com'
            ],
            [
                'key' => 'social_instagram',
                'value' => 'instagram.com'
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'linkedin.com'
            ],
            [
                'key' => 'social_youtube',
                'value' => 'youtube.com'
            ],
            [
                'key' => 'social_skype_name',
                'value' => '#skype_social_name'
            ],
            [
                'key' => 'social_instagram_name',
                'value' => '#social_name'
            ],
        ];


        Setting::insert($settings);

    }
}
