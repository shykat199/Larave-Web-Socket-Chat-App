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
    public $senderId;
    public $isTyping;
    public $message;
    /**
     * Create a new event instance.
     */
    public function __construct($message,$receiverId,$senderId,$isTyping)
    {
        $this->receiverId = $receiverId;
        $this->senderId = $senderId;
        $this->isTyping = $isTyping;
        $this->message = $message;
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'isTyping' => $this->isTyping,
            'receiverId' => $this->receiverId,
            'senderId' => $this->senderId,
        ];
    }

    public function broadcastAs(): string
    {
        return 'typingEvent';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('typing'),
        ];
    }
}
