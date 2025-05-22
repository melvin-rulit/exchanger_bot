<?php

namespace App\Services\AmountService;

use App\DTO\CallbackTelegramData;
use App\Enums\Amount\AmountField;
use App\Validators\AmountValidator;
use App\Enums\TelegramCallbackAction;
use App\Exceptions\TelegramApiException;
use App\Handlers\RequisiteCallbackHandler;
use App\Telegram\Keyboard\KeyboardFactory;
use App\Services\OrderService\OrderService;
use App\Services\RedisService\RedisFieldService;
use App\Services\TelegramBotService\TelegramMessageService;

class AmountService
{
    public function __construct(protected TelegramMessageService $telegramMessageService, protected RedisFieldService $redisField, protected OrderService $orderService, protected RequisiteCallbackHandler $requisiteCallbackHandler,){}

    /**
     * @throws TelegramApiException
     */
    public function processAmountCallback(CallbackTelegramData $callback): void
    {
        if (!AmountValidator::isValid($callback)) {

            $this->sendInvalidAmountMessage(
                chatId: $callback->chatId,
                messageId: $callback->messageId
            );
            return;
        }

        $this->checkProcessAmount($callback->chatId, $callback->clientBotId, $callback->text, $callback->messageId);
    }

    /**
     * @throws TelegramApiException
     */
    public function checkProcessAmount($chatId, $clientId, $amount, $messageId): void
    {
        $this->requisiteCallbackHandler->handle($chatId, $clientId, $messageId);

        $order = $this->orderService->saveOrder($chatId, $clientId, $amount);
        $this->redisField->setOrderId($chatId, $order->id, 0);
    }

    /**
     * @throws TelegramApiException
     */
    public function sendInvalidAmountMessage(int $chatId, int $messageId): void
    {
        $keyboard['inline_keyboard'][] = KeyboardFactory::toConsultation(TelegramCallbackAction::ToConsultation->value. AmountField::AMOUNT->value);
        $keyboard['inline_keyboard'][] = KeyboardFactory::toBack(TelegramCallbackAction::SelectAmountBack->value);
        $this->telegramMessageService->sendMessageWithButtons($chatId, __('messages.enter_the_amount_correct_numbers'), $keyboard, $messageId);
    }
}
