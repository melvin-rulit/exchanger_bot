<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public Order $order;
    public ?string $eventType;

    public function __construct(Order $order, $eventType = null)
    {
        $this->order = $order;
        $this->eventType = $eventType ?? 'default';
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('update_order'),
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id
            ],
            'type' => $this->eventType ?? 'default',
        ];
    }
    public function broadcastAs(): string
    {
        return 'order-updated';
    }
    public function shouldBroadcastNow(): bool
    {
        return true;
    }
}
