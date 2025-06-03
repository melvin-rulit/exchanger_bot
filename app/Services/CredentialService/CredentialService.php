<?php

namespace App\Services\CredentialService;

use App\Models\Country;

class CredentialService
{
    public function checkAmountInput($amount): ?string
    {
        $credential = null;
//TODO $this->getCredential(1); передавать правильно id страны, по которым выдаются тот или иной реквизит
        if ($amount > 18000) {
            $credential = $this->getCredential(1);
        }else {
            $credential = $this->getCredential(2);
        }

        return $credential;
    }

    public function getCredential($countryId): ?string
    {
        $country = Country::find($countryId);

        if (!$country) {
            return null;
        }

        return $country->getCredentialValueAttribute();
    }
}
