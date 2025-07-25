<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChekSend implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Order $order, public ?string $eventType = null){}


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('update_order'),
        ];
    }
    public function broadcastWith(): array
    {
        $orderWithRelations = $this->order->load('client', 'user');

        return [
            'order' => $orderWithRelations,
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
