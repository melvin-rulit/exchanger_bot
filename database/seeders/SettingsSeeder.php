<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'language',
                'value' => 'ru',
            ],
            [
                'key' => 'language',
                'value' => 'en',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate([
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }
    }
}
