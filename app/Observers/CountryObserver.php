<?php

namespace App\Observers;

use App\Models\Country;
use App\Models\Currency;

class CountryObserver
{
    public function created(Country $country)
    {
        Currency::create([
            'name' => 'Local Currency',
            'symbol' => null,
            'country_id' => $country->id,
        ]);
        Currency::create([
            'name' => 'Dollar',
            'symbol' => '$',
            'country_id' => $country->id,
        ]);
        Currency::create([
            'name' => 'Euro',
            'symbol' => '€',
            'country_id' => $country->id,
        ]);
    }
}
