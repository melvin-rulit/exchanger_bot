<?php

namespace App\Handlers\Callback;

use App\DTO\BankSelectionData;
use App\Actions\StartBankAction;
use App\Exceptions\TelegramApiException;
use App\Exceptions\Country\CountryNotFoundException;
use App\Exceptions\Country\CountryBankNotFoundException;

class BankCallbackHandler
{
    public function __construct(protected StartBankAction $action) {}

    /**
     * @throws CountryNotFoundException
     * @throws TelegramApiException
     * @throws CountryBankNotFoundException
     */
    public function handle(string $countryCode, int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new BankSelectionData($countryCode, $clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
