<?php

namespace App\Actions\Menu;

use App\DTO\ConsultationData;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\ClientService\ClientsService;
use App\Services\RedisService\RedisSessionService;
use App\Services\TelegramBotService\TelegramMessageService;


class StartConsultationAction
{
    public function __construct(protected RedisSessionService $redis, protected ClientsService $clientsService, protected TelegramMessageService $telegramMessageService) {}

    public function execute(ConsultationData $data)
    {
        try {
            $this->clientsService->setClientConsultationInput($data->chatId, $data->clientId);
            $messageGroup = $this->redis->getMessageGroupForConsultation($data->chatId);

            if (!$messageGroup) {
                $messageGroup = generateRandomDigits(6);
                $this->redis->setMessageGroupForConsultation($data->chatId, $messageGroup, 0);

                if (!$this->redis->getMessageGroupForConsultation($data->chatId)) {
                    log_error('❌ Не удалось получить message_group для консультации', [
                        'file' => 'StartConsultationAction',
                        'row' => '26',
                    ], 'redis');

                    return null;
                }
            }

            $keyboard = KeyboardFactory::toMain();
            $this->telegramMessageService->sendMessageWithButtons($data->chatId, __('messages.consultation'), $keyboard, $data->messageId);
        }
        catch (\Throwable $e) {
            log_error('❌ Не удалось записать message_group для консультации', [
                'chat_id' => $data->chatId,
                'client_id' => $data->clientId,
                'exception' => $e->getMessage(),
            ], 'redis');

            return null;
        }
    }
}
