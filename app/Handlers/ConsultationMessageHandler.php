<?php

namespace App\Handlers;

use App\Actions\Menu\StartConsultationAction;
use App\DTO\ConsultationData;

class ConsultationMessageHandler
{
    public function __construct(protected StartConsultationAction $startConsultationAction) {}

    public function handle(int $chatId, int $clientId, int $messageId): ?string
    {
        $dto = new ConsultationData($chatId, $clientId, $messageId);
        return $this->startConsultationAction->execute($dto);
    }
}
