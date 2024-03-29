<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $messages;
    private $userInformations;

    /**
     * Create a new event instance.
     */
    public function __construct($messages, $userInformations)
    {
        $this->messages = $messages;
        $this->userInformations = $userInformations;
    }

    public function broadcastWith()
    {
        return [
            'chat' => $this->messages,
            'userInformations' => $this->userInformations,
            'userImage' => isset($this->userInformations->sender->user_image)?asset('storage/user-image/'.$this->userInformations->sender->user_image):'https://bootdey.com/img/Content/avatar/avatar3.png',
        ];
    }

    public function broadcastAs(): string
    {
        return 'getChatMessage';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('send-message'),
        ];
    }
}
