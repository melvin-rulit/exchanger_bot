<?php

namespace App\Handlers\Callback;

use App\Actions\StartCurrencyAction;
use App\DTO\CurrencySelectionData;
use Illuminate\Support\Facades\Log;

class CurrencyCallbackHandler
{
    public function __construct(protected StartCurrencyAction $action) {}

    public function handle(int|string $currencyId, int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new CurrencySelectionData($currencyId, $clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
