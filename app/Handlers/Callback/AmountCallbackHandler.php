<?php

namespace App\Handlers\Callback;

use App\DTO\AmountSelectionData;
use App\Actions\StartAmountAction;
use App\Exceptions\TelegramApiException;


class AmountCallbackHandler
{
    public function __construct(protected StartAmountAction $action) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(int $bankId, int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new AmountSelectionData($bankId, $clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
