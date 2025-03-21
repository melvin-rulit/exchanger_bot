<?php

namespace App\Services\OrderService;

interface OrderServiceInterface
{
    public function saveOrder($chatId, $clientId, $amount, $currency_id);
    public function getOrdersForClient($clientId);
    public function setOrderStatus($orderId, $status);
}
