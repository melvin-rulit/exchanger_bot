<?php

namespace App\Telegram\Handlers;

use App\Models\Order;
use App\Events\Order\ChekSend;
use App\Telegram\Traits\SavePhoto;
use App\Telegram\Traits\HandlesFile;
use App\Exceptions\TelegramApiException;
use App\Services\ClientService\ClientsService;
use App\Exceptions\Images\MediaLibraryException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Handlers\Callback\WalletCallbackHandler;
use App\Services\RedisService\RedisFieldService;
use App\Exceptions\Helpers\InvalidStringValueException;

class ReceiptHandler
{
    use SavePhoto, HandlesFile;

    protected string $url;
    protected string $download_url;

    /**
     * @throws InvalidStringValueException
     */
    public function __construct(protected ClientsService $clientsService, protected RedisFieldService $redisField, protected WalletCallbackHandler $walletCallbackHandler)
    {
        $this->url = ensure_string(config('telegram.telegram_bot.api_url'), 'telegram.telegram_bot.api_url') . ensure_string(config('telegram.telegram_bot.token'), 'telegram.telegram_bot.token');
        $this->download_url = ensure_string(config('telegram.telegram_bot.api_file_url')) . config('telegram.telegram_bot.token');
    }

    /**
     * @throws TelegramApiException
     * @throws MediaLibraryException
     * @throws OrderNotFoundException
     */
    public function prepareSendCheck($fileData, $clientId, $chatId): void
    {
        setAppLanguage($this->clientsService->getClientLanguage($clientId));

        $fileId = $fileData['file_id'];

        $imageContent = $this->getTelegramFileContent($fileId);

        $order = Order::where('id', $this->redisField->getOrderId($chatId))->first();

        if (!$order) {
            throw new OrderNotFoundException("Заказ с ID {$this->redisField->getOrderId($chatId)} не найден.");
        }

        $this->saveImageToModelFromResponse($imageContent, 'check.jpg', $order, 'amount_check');
        $order->refresh();
        broadcast(new ChekSend($order, 'send_chek'));

////TODO понят что нужно передавать messageId
      $this->walletCallbackHandler->handle($clientId, $chatId, 564);
    }
}
