<?php

namespace App\Observers;

use App\Models\Order;
use App\Events\Order\OrderUpdated;

class OrderObserver
{
    public function updated(Order $order): void
    {
//        if ($order->isDirty('user_id') && $order->user_id !== auth()->id()) {
//            // 🔔 Событие вызывается при назначении менеджера заказу, чтобы обновить интерфейс на фронте (orderList)
//            event(new OrderUpdated($order, 'attach_user'));
//        }

        if ($order->isDirty('user_id')) {
            event(new OrderUpdated($order, 'attach_user'));
        }

        if ($order->isDirty('status')) {
            event(new OrderUpdated($order));
        }
    }
}
