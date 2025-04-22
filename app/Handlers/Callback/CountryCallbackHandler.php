<?php

namespace App\Handlers\Callback;

use App\DTO\CountrySelectionData;
use App\Actions\StartCountryAction;

class CountryCallbackHandler
{
    public function __construct(protected StartCountryAction $action) {}

    public function handle(string $countryCode, int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new CountrySelectionData($countryCode, $clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
