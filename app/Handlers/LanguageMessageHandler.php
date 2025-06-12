<?php

namespace App\Handlers;

use App\Actions\Menu\StartLanguageAction;
use App\DTO\LanguageSelectionData;
use App\Exceptions\TelegramApiException;

class LanguageMessageHandler
{
    public function __construct(protected StartLanguageAction $action) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(int|string $chatId, int $clientId, int $messageId): void
    {
        $dto = new LanguageSelectionData($chatId, $clientId, $messageId);
        $this->action->execute($dto);
    }
}
