<?php

namespace App\Handlers\Callback\MenuHandlers;

use App\DTO\RequisiteSelectionData;
use App\Exceptions\TelegramApiException;
use App\Actions\Menu\StartRequisiteAction;
use App\Exceptions\Country\CountryNotFoundException;

class GetRequisiteCallbackHandler
{
    public function __construct(protected StartRequisiteAction $action){}

    /**
     * @throws TelegramApiException|CountryNotFoundException
     */
    public function handle(int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new RequisiteSelectionData($clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
