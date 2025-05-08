<?php

namespace Database\Seeders\Country;

use App\Models\Bank;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CountryBankSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $moldova = Country::where('code', 'MD')->first();
        $georgia = Country::where('code', 'GE')->first();

        $moldBank = Bank::where('name', 'MoldBank')->first();
        $grusiaBank = Bank::where('name', 'GrusiaBank')->first();

        if ($moldova && $moldBank) {
            $moldova->banks()->syncWithoutDetaching([$moldBank->id]);
        }

        if ($georgia && $grusiaBank) {
            $georgia->banks()->syncWithoutDetaching([$grusiaBank->id]);
        }
    }
}
