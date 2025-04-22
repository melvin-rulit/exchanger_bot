<?php

namespace App\Handlers\Callback\MenuHandlers;

use App\DTO\LanguageSelectionData;
use App\Actions\StartLanguageAction;

class GetLanguageCallbackHandler
{
    public function __construct(protected StartLanguageAction $action) {}

    public function handle(int $chatId, int $clientId, int $messageId): void
    {
        $dto = new LanguageSelectionData($chatId, $clientId, $messageId);
        $this->action->execute($dto);
    }
}
