<?php

namespace App\Services;

use App\Models\Client;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Helpers\InvalidStringValueException;

abstract class BaseService
{
    protected string $url;

    /**
     * @throws InvalidStringValueException
     */
    public function __construct(protected RedisSessionService $redis)
    {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url') . ensure_string(config('telegram.telegram_bot.token'), 'telegram.telegram_bot.token');
    }

    public function getClientByBotId($clientBotId)
    {
        return Client::where('bot_id', $clientBotId)->first();
    }
    public function setStatus($clientBotId, $status): true
    {
        $client = $this->getClientByBotId($clientBotId);

        if ($client) {
            $client->status = $status;
            $client->save();
        }
        return true;
    }
    public function getClientStatus($clientBotId)
    {
        $client = $this->getClientByBotId($clientBotId);
        return $client?->status;
    }
}
