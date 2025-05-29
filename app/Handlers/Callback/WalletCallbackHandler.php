<?php

namespace App\Handlers\Callback;

use App\DTO\WalletSelectionData;
use App\Actions\StartWalletAction;
use App\Exceptions\TelegramApiException;


class WalletCallbackHandler
{
    public function __construct(protected StartWalletAction $action) {}

    /**
     * @throws TelegramApiException
     */
    public function handle(int $clientId, int $chatId, int $messageId): void
    {
        $dto = new WalletSelectionData($clientId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
