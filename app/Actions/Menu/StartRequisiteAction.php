<?php

namespace App\Actions\Menu;

use App\DTO\CountrySelectionData;
use App\DTO\RequisiteSelectionData;
use App\Actions\StartCountryAction;
use App\Exceptions\TelegramApiException;
use App\Services\ClientService\ClientsService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Services\RedisService\RedisMessageService;
use App\Services\TelegramBotService\TelegramMessageService;

class StartRequisiteAction
{
    public function __construct(protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService, protected StartCountryAction $startCountryAction, protected RedisMessageService $redisMessageService) {}

    /**
     * @throws TelegramApiException|CountryNotFoundException
     */
    public function execute(RequisiteSelectionData $data): void
    {
        $this->redisMessageService->setDeletedMessageForChat($data->chatId, $data->messageId);

        $countryData = new CountrySelectionData(
            clientBotId: $data->clientBotId,
            chatId: $data->chatId,
            messageId: $data->messageId,
        );

        $this->startCountryAction->execute($countryData);
    }
}
