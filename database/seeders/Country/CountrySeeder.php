<?php

namespace Database\Seeders\Country;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $countries = [
            [
                'name_ru' => 'Молдова',
                'name_en' => 'Moldova',
                'code' => 'MD',
                'flag' => '🇲🇩',
                'is_used' => true,
            ],
            [
                'name_ru' => 'Грузия',
                'name_en' => 'Georgia',
                'code' => 'GE',
                'flag' => '🇬🇪',
                'is_used' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
