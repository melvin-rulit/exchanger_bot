<?php

namespace App\Handlers\Callback;

use App\DTO\CountrySelectionData;
use App\Actions\StartCountryAction;
use App\Exceptions\TelegramApiException;
use App\Exceptions\Country\CountryNotFoundException;

class CountryCallbackHandler
{
    public function __construct(protected StartCountryAction $action) {}

    /**
     * @throws TelegramApiException
     * @throws CountryNotFoundException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new CountrySelectionData($clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
