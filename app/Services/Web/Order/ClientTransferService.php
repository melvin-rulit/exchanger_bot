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

    public function extractRequisite(string $message, string $type): array
    {
        $requisite = null;
        $clientFullName = null;
        $IDNP = null;

        \Log::info($message);
        switch ($type) {
            case 'iban': // ✅ Тип 1
                preg_match('/IBAN:\s*([A-Z0-9]+)/u', $message, $ibanMatch);
                $requisite = $ibanMatch[1] ?? null;

                if ($requisite) {
                    $rest = trim(str_replace($ibanMatch[0], '', $message));
                    $lines = preg_split('/\r?\n/', $rest, -1, PREG_SPLIT_NO_EMPTY);

                    if (isset($lines[0])) {
                        $IDNP = trim($lines[0]);
                    }
                    if (isset($lines[1])) {
                        $clientFullName = trim($lines[1]);
                    }
                }
                break;

            case 'phoneUser': // ✅ Тип 2
                preg_match('/Телефон:\s*(\d{6,15})/u', $message, $phoneMatch);

                $requisite = $phoneMatch[1] ?? null;
                if ($phoneMatch) {
                    $clientFullName = trim(str_replace($phoneMatch[0], '', $message));
                }
                break;

            case 'cardUser': // ✅ Тип 3
                preg_match('/Карта:\s*(\d{6,19})/u', $message, $cardMatch);

                if ($cardMatch) {
                    $requisite = $cardMatch[1];
                    $clientFullName = trim(str_replace($cardMatch[0], '', $message));
                }
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

        $parsed = $this->extractRequisite($message, $type);

        $requisite = $parsed['requisite'];
        $clientFullName = $parsed['fullName'];
        $IDNP = $parsed['IDNP'];

        if (!$requisite) {
            throw new \RuntimeException('Не удалось извлечь реквизит из сообщения');
        }

        $messageHead = 'Отправьте ' .  $order->amount .  ' на эти реквизиты ☝️и прикрепите чек';

        $keyboard['inline_keyboard'][] = KeyboardFactory::toCancel(true);

        //$this->telegramMessageService->sendDeleteReplay($order->chat_id, $messageHead);
        $this->telegramMessageService->sendMessage($order->chat_id, $requisite);
        if ($type === 'iban' && $IDNP) {
            $this->telegramMessageService->sendMessage($order->chat_id, $IDNP);
        }
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
