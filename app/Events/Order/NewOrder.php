<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewOrder implements ShouldBroadcast
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
            new Channel('new_order'),
        ];
    }
    public function broadcastWith(): array
    {
        $orderWithRelations = $this->order->load('client');

        return [
            'order' => $orderWithRelations,
            'type' => $this->eventType ?? 'default',
        ];
    }
    public function broadcastAs(): string
    {
        return 'new_order';
    }
    public function shouldBroadcastNow(): bool
    {
        return true;
    }
}
