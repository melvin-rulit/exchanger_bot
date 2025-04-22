<?php

namespace App\Handlers\Callback\MenuHandlers;

use App\Actions\StartConsultationAction;
use App\DTO\ConsultationData;
use Illuminate\Support\Facades\Log;

class GetConsultationCallbackHandler
{
    public function __construct(protected StartConsultationAction $action) {}

    public function handle(int $clientId, int $chatId, int $messageId): ?string
    {
        $dto = new ConsultationData($chatId, $clientId, $messageId);
        return $this->action->execute($dto);
    }
}
