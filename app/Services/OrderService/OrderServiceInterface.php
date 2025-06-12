<?php

namespace App\Services\OrderService;

interface OrderServiceInterface
{
    public function saveOrder($chatId, $clientId, $amount);
    public function changeStatus($orderId, $status);
}
