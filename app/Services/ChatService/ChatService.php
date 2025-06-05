<?php

namespace App\Services\ChatService;

use App\Models\Order;
use App\Models\Message;
use App\Events\Order\OrderMessageSent;
use App\Events\Consultation\ClientConsultationMessageSent;

class ChatService
{
    public function setMessageInput($orderId): void
    {
        $order = Order::find($orderId);

        if ($order) {
            $order->is_message = true;
            $order->save();

            broadcast(new OrderMessageSent($order));
        }
    }

    public function prepareSaveMessage(int $chatId ,int $clientId, ?int $messageGroup = null, ?string $photoFileId = null, ?string $message = null, ?int $saveOrderId = null): ?Message {
        if ($photoFileId) {
            broadcast(new ClientConsultationMessageSent($chatId, $message));

            if ($saveOrderId) {
                $this->setMessageInput($saveOrderId);
                return $this->createEmptyImageMessage($chatId, $clientId, $messageGroup, $saveOrderId);
            }
            return $this->createEmptyImageMessage($chatId, $clientId, $messageGroup, null);
        }

        if ($saveOrderId) {
            $this->saveMessage($chatId, $clientId, $message, $saveOrderId, $messageGroup);
            $this->setMessageInput($saveOrderId);
            return null;
        }

        $createdMessage = $this->saveMessage($chatId, $clientId, $message, null, $messageGroup);
        broadcast(new ClientConsultationMessageSent($chatId, $message));

        return $createdMessage;
    }

    public function saveMessage(int $chatId, int $clientId, ?string $message, ?int $orderId, ?int $messageGroup = null): Message
    {
        return Message::create([
            'message_group' => $messageGroup,
            'chat_id'       => $chatId,
            'order_id'      => $orderId,
            'client_id'      => $clientId,
            'user_id'       => null,
            'sender_type'   => 'client',
            'message'       => $message,
        ]);
    }

    public function createEmptyImageMessage(int $chatId, int $clientId, int $messageGroup, $saveOrderId): Message
    {
        return Message::create([
            'message_group' => $messageGroup,
            'chat_id'       => $chatId,
            'order_id'      => $saveOrderId,
            'client_id'      => $clientId,
            'user_id'       => null,
            'sender_type'   => 'client',
            'message'       => null,
        ]);
    }
}
