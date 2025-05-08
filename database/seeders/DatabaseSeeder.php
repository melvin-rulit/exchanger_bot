<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Country\CountrySeeder;
use Database\Seeders\Country\CountryBankSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            BankSeeder::class,
            CountryBankSeeder::class,
        ]);

        $this->call(RolesSeeder::class);
        $this->call(SettingsSeeder::class);
    }
}
