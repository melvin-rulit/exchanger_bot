<?php

namespace App\Services;

use App\Models\Client;

abstract class BaseService
{
    public function __construct(protected RedisSessionService $redis){}

    public function getClientByBotId($clientBotId)
    {
        return Client::where('bot_id', $clientBotId)->first();
    }
    public function setStatus($clientBotId, $status): void
    {
        $client = $this->getClientByBotId($clientBotId);

        if ($client) {
            $client->status = $status;
            $client->save();
        }
    }
    public function getClientStatus($clientBotId)
    {
        $client = $this->getClientByBotId($clientBotId);
        return $client?->status;
    }
}
