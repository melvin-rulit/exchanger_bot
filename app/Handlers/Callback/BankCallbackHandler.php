<?php

namespace App\Handlers\Callback;

use App\DTO\BankSelectionData;
use App\Actions\StartBankAction;

class BankCallbackHandler
{
    public function __construct(protected StartBankAction $action) {}

    public function handle(int $bankId, int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new BankSelectionData($bankId, $clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
