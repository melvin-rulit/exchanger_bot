<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $banks = [
            [
                'name' => 'MoldBank',
            ],
            [
                'name' => 'GrusiaBank',
            ],
        ];

        foreach ($banks as $bank) {
            Bank::firstOrCreate(
                ['name' => $bank['name']],
                $bank
            );
        }
    }
}
