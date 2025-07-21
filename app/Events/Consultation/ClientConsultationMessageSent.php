<?php

namespace App\Events\Consultation;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Consultation\ChatMessageResource;

class ClientConsultationMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $chat_id, public ChatMessageResource $message, public ?string $eventType = null){}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('consultation'),
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chat_id,
            'message' => $this->message,
        ];
    }
    public function broadcastAs(): string
    {
        return 'new_message';
    }
    public function shouldBroadcastNow(): bool
    {
        return true;
    }
}
