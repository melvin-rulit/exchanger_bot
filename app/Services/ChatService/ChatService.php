<?php

namespace App\Services\ChatService;

use App\Events\ClientConsultationMessageSent;
use App\Events\OrderUpdated;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Container\Attributes\Log;

class ChatService
{
    public function setMessageInput($order_id)
    {
        $order = Order::find($order_id);

        if ($order) {
            $order->is_message = true;
            $order->save();

            broadcast(new OrderUpdated($order));
        }

    }

    public function prepareSaveMessage($message, $chatId, $save_order_id = null)
    {
        if ($save_order_id) {
            $this->saveMessage($save_order_id, $message, $chatId);
            $this->setMessageInput($save_order_id);
        } else {
            $this->saveMessage($save_order_id, $message, $chatId);
            broadcast(new ClientConsultationMessageSent($message, $chatId));
        }

    }

    public function saveMessage($save_order_id, $message, $chatId)
    {
        Message::create([
            'chat_id' => $chatId,
            'order_id' => $save_order_id,
            'user_id' => 6,
            'sender_type' => 'client',
            'message' => $message,
        ]);
    }
}
