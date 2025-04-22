<?php

namespace App\Handlers\Callback\MenuHandlers;

use App\DTO\RequisiteSelectionData;
use App\Actions\StartRequisiteAction;
use Illuminate\Support\Facades\Log;

class GetRequisiteCallbackHandler
{
    public function __construct(protected StartRequisiteAction $action){}

    public function handle(int $clientBotId, int $chatId, int $messageId): void
    {
        $dto = new RequisiteSelectionData($clientBotId, $chatId, $messageId);
        $this->action->execute($dto);
    }
}
