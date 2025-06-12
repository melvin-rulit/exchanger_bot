<?php

namespace App\Telegram\Handlers;

use App\Enums\MenuLevelStatus;
use App\DTO\CallbackReturnMenu;
use App\Telegram\Traits\SavePhoto;
use App\Telegram\Traits\HandlesFile;
use App\Exceptions\TelegramApiException;
use App\Services\ChatService\ChatService;
use App\Services\AmountService\AmountService;
use App\Services\ClientService\ClientsService;
use App\Telegram\Route\CallbackMenuReturnRouter;
use App\Services\RedisService\RedisFieldService;
use App\Exceptions\Images\MediaLibraryException;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Country\CountryNotFoundException;
use App\Exceptions\Helpers\InvalidStringValueException;
use App\Exceptions\Country\CountryBankNotFoundException;

class ConsultationHandles
{
    use SavePhoto, HandlesFile;

    protected string $url;
    protected string $download_url;

    /**
     * @throws InvalidStringValueException
     */
    public function __construct(protected ChatService $chatService, protected ClientsService $clientsService, protected AmountService $amountService, protected RedisSessionService $redis, protected RedisFieldService $redisField, protected CallbackMenuReturnRouter $callbackMenuReturnRouter)
    {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url') . ensure_string(config('telegram.telegram_bot.token'), 'telegram.telegram_bot.token');
        $this->download_url = ensure_string(config('telegram.telegram_bot.api_file_url')) . config('telegram.telegram_bot.token');
    }


    /**
     * @throws TelegramApiException
     * @throws CountryBankNotFoundException
     * @throws CountryNotFoundException
     * @throws MediaLibraryException
     */
    public function handleTextMessageIfInConsultation($data, $callback, $inConsultation, ?int $orderId = null, ?string $menuLevel = null): void
    {
        if (!empty($data['message']['text']) && $data['message']['text'] !== __('buttons.back_menu')) {

            if ($inConsultation) {
                $this->chatService->prepareSaveMessage(
                    $callback->chatId,
                    $this->clientsService->getClient($callback->chatId, $callback->clientBotId),
                    $this->redis->getMessageGroupForConsultation($callback->chatId),
                    null,
                    $callback->text,
                    $orderId
                );
            }else {
               if ($menuLevel === MenuLevelStatus::Amount->value){
                   $this->amountService->processAmountCallback($callback);
               }
            }

            return;
        }

        if (!empty($data['message']['photo']) && (!isset($data['message']['text']) || $data['message']['text'] !== __('buttons.back_menu'))) {

            if ($inConsultation) {
                $message_group = $this->redis->getMessageGroupForConsultation($callback->chatId);
                $this->savePhoto(getLargestPhoto($callback->photos), $this->redis->getClientIdForChat($callback->chatId), $callback->chatId, $message_group, $orderId);
            }
            else {
                if ($menuLevel === MenuLevelStatus::Amount->value){
                    $this->amountService->processAmountCallback($callback);
                }
            }
            return;
        }

        $callbackData = CallbackReturnMenu::fromTelegram(['callbackReturnMenuKey' => $callback->text, 'clientBotId' => $callback->clientBotId, 'clientStatus' => $this->clientsService->getClientStatus($callback->clientBotId)]);
        $this->callbackMenuReturnRouter->route($callbackData, $callback->clientBotId, $callback->chatId, $callback->messageId);
    }
}
