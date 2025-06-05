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

    public function extractRequisitesFromMessage(string $message): ?string
    {
        preg_match('/\b\d{5,}\b/', $message, $matches);
        return $matches[0] ?? null;
    }

    /**
     * @throws TelegramApiException
     * @throws OrderClientNotFoundException
     */
    public function handleRequisiteRequest(Order $order, $chatId, $message): void
    {
        setAppLanguage($this->clientsService->getClientLanguage($order->chat_id));

        $requisite = $this->extractRequisitesFromMessage($message);

        $messageHtml = <<<HTML
        Отправьте <i>$order->amount</i> на эти реквизиты и прикрепите чек.
        <pre>
          $requisite
        </pre>
        <a href="https://example.com">Как пополнить?</a>
HTML;
        //$keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value . BankField::BANK->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toCancel(true);

        //$this->telegramMessageService->deleteMessages($chatId, $message);
        $this->telegramMessageService->sendMessageWithButtons($order->chat_id, $messageHtml, $keyboard);

        if (!$order->client) {
            throw new OrderClientNotFoundException("Заказу с ID {$order->id} не присвоен клиент");
        }

        $order->setIsRequisite();

        $order->client()->update([
            'status' => MenuLevelStatus::Screenshot->value,
        ]);
    }
}
