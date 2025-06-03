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

    public function __construct(public Order $order, public ?string $eventType = null){}


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
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
