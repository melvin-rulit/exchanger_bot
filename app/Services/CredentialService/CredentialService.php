<?php

namespace App\Services\CredentialService;

use App\Events\ClientConsultationMessageSent;
use App\Events\OrderUpdated;
use App\Models\Country;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Container\Attributes\Log;

class CredentialService
{
    public function checkAmountInput($amount): ?int
    {
        $credential = null;

        if ($amount > 18000) {
            $credential = $this->getCredential(1);
        }else {
            $credential = $this->getCredential(2);
        }

        return $credential;
    }

    public function getCredential($country_id)
    {
        $country = Country::find($country_id);

        if ($country) {
            $credentials = $country->credentials()->first();

            if ($credentials) {
                return $credentials->value;
            }
        }

        return null;
    }
}
