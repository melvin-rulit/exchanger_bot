<?php

namespace App\Events\Consultation;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class ConsultationClosed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public function __construct(){}


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('consultation_closed'),
        ];
    }
    public function broadcastWith(): array
    {
        return [

        ];
    }
    public function broadcastAs(): string
    {
        return 'consultation_closed';
    }
    public function shouldBroadcastNow(): bool
    {
        return true;
    }
}
