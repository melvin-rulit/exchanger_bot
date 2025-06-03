<?php

namespace App\Handlers\Callback\MenuHandlers;

use App\DTO\LanguageSelectionData;
use App\Exceptions\TelegramApiException;
use App\Actions\Menu\StartLanguageAction;

class GetLanguageCallbackHandler
{
    public function __construct(protected StartLanguageAction $action) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(int $chatId, int $clientId, int $messageId): void
    {
        $dto = new LanguageSelectionData($chatId, $clientId, $messageId);
        $this->action->execute($dto);
    }
}
