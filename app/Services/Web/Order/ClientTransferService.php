<?php

namespace App\Services\Web\Order;

use App\Models\Order;
use App\Enums\MenuLevelStatus;
use App\Services\Web\BaseWebService;
use App\Exceptions\TelegramApiException;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Telegram\Traits\SendsFakeWebhookCommandTrait;
use App\Exceptions\Order\OrderClientNotFoundException;

class ClientTransferService extends BaseWebService
{
    use SendsFakeWebhookCommandTrait;

    public function extractRequisitesAndName(string $message): array
    {
        preg_match('/\b(\d{5,})\b(.*)/u', $message, $matches);

        $requisite = isset($matches[1]) ? trim($matches[1]) : null;
        $clientFullName = isset($matches[2]) ? trim($matches[2]) : null;

        return [
            'requisite' => $requisite,
            'fullName' => $clientFullName,
        ];
    }


    /**
     * @throws TelegramApiException
     * @throws OrderClientNotFoundException
     */

    public function handleRequisiteRequest(Order $order, $chatId, $message): void
    {
        setAppLanguage($this->clientsService->getClientLanguage($order->chat_id));

        $parsed = $this->extractRequisitesAndName($message);
        $requisite = $parsed['requisite'];
        $clientFullName = $parsed['fullName'] ?? 'ФИ';

        if (!$requisite) {
            throw new \RuntimeException('Не удалось извлечь реквизит из сообщения');
        }
$messageHead = 'Отправьте ' .  $order->amount .  ' на эти реквизиты ☝️и прикрепите чек';
//        $messageHtml = <<<HTML
//Отправьте <i>$order->amount</i> на эти реквизиты и прикрепите чек.
//<pre>
//  $requisite
//  $clientFullName
//</pre>
//
//HTML;

        $keyboard['inline_keyboard'][] = KeyboardFactory::toCancel(true);

        //$this->telegramMessageService->sendDeleteReplay($order->chat_id, $messageHead);
        $this->telegramMessageService->sendMessage($order->chat_id, $requisite);
        $this->telegramMessageService->sendMessage($order->chat_id, $clientFullName);
        $this->telegramMessageService->sendMessageWithButtons($order->chat_id, $messageHead, $keyboard);

        if (!$order->client) {
            throw new OrderClientNotFoundException("Заказу с ID {$order->id} не присвоен клиент");
        }

        $order->setIsRequisite();

        $order->client()->update([
            'status' => MenuLevelStatus::Screenshot->value,
        ]);
    }

}
