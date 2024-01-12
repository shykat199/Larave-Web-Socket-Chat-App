<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $receiverId;
    public $isTyping;
    /**
     * Create a new event instance.
     */
    public function __construct($receiverId, $isTyping)
    {
        $this->receiverId = $receiverId;
        $this->isTyping = $isTyping;
    }

    public function broadcastWith()
    {
        return [
            'receiverId' => $this->receiverId,
            'isTyping' => $this->isTyping
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('typingEvent'),
        ];
    }
}
