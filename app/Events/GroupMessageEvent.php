<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $groupChat;
    private $userImage;
    /**
     * Create a new event instance.
     */
    public function __construct($groupChat,$userImage)
    {
        $this->groupChat=$groupChat;
        $this->userImage=$userImage;
    }

    public function broadcastWith()
    {
        return [
            'chat' => $this->groupChat,
            'userImage' => $this->userImage,
        ];
    }

    public function broadcastAs(): string
    {
        return 'getGroupChat';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('get-group-chat'),
        ];
    }
}
