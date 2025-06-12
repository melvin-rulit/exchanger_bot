<?php

namespace App\Services\TelegramBotService;

use Exception;
use App\Telegram\Handlers\AbstractTelegramHandler;

class TelegramBotService extends AbstractTelegramHandler implements TelegramBotServiceInterface
{
    /**
     * @throws Exception
     */
    public function getWebchook($data): void
    {
        $this->loadOut($data);
    }
}
