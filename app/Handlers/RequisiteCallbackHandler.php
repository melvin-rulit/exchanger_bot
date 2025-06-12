<?php

namespace App\Handlers;

use App\DTO\RequisiteSelectionData;
use App\Actions\StartRequisiteAction;
use App\Exceptions\TelegramApiException;

class RequisiteCallbackHandler
{
    public function __construct(protected StartRequisiteAction $action) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(int $chatId, int $clientBotId, int $messageId): void
    {

        $dto = new RequisiteSelectionData($chatId, $clientBotId, $messageId);
        $this->action->execute($dto);
    }
}
