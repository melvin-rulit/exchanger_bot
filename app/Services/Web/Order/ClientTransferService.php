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

    public function extractRequisitesAndName(string $message, string $type): array
    {
        $requisite = null;
        $clientFullName = null;
        $IDNP = null;

        switch ($type) {
            case 'iban': // ✅ Тип 1
                // Получатель: TODOROV IVAN
                // IDNP: 2002500106846
                // IBAN: MD41AG000000022595902591
                preg_match('/IBAN:\s*([A-Z0-9]+)/u', $message, $ibanMatch);
                preg_match('/Получатель:\s*(.+)/u', $message, $nameMatch);
                preg_match('/IDNP:\s*(\d+)/u', $message, $idnpMatch);

                $requisite = $ibanMatch[1] ?? null;
                $clientFullName = $nameMatch[1] ?? null;
                $IDNP = $idnpMatch[1] ?? null;
                break;

            case 'phoneUser': // ✅ Тип 2
                // Mia: 68165847
                // Получатель: TODOROV IVAN
                preg_match('/Mia:\s*(\d+)/u', $message, $miaMatch);
                preg_match('/Получатель:\s*(.+)/u', $message, $nameMatch);

                $requisite = $miaMatch[1] ?? null;
                $clientFullName = $nameMatch[1] ?? null;
                break;

            case 'cardUser': // ✅ Тип 3
                // Номер карты: 7584858585858484
                // Получатель: Иван Иванов
                preg_match('/Номер карты:\s*(\d{12,19})/u', $message, $cardMatch);
                preg_match('/Получатель:\s*(.+)/u', $message, $nameMatch);

                $requisite = $cardMatch[1] ?? null;
                $clientFullName = $nameMatch[1] ?? null;
                break;
        }

        return [
            'requisite' => $requisite,
            'fullName' => $clientFullName,
            'IDNP' => $IDNP,
        ];
    }

    /**
     * @throws TelegramApiException
     * @throws OrderClientNotFoundException
     */

    public function handleRequisiteRequest(Order $order, $chatId, $message, $type): void
    {
        setAppLanguage($this->clientsService->getClientLanguage($order->chat_id));

        $parsed = $this->extractRequisitesAndName($message, $type);

        $requisite = $parsed['requisite'];
        $clientFullName = $parsed['fullName'] ?? 'ФИ';
        $IDNP = $parsed['IDNP'];

        if (!$requisite) {
            throw new \RuntimeException('Не удалось извлечь реквизит из сообщения');
        }

        $messageHead = 'Отправьте ' .  $order->amount .  ' на эти реквизиты ☝️и прикрепите чек';

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
