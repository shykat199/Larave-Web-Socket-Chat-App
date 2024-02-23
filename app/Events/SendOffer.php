<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendOffer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;
    public $type;
    public $sender_id;
    public $receiver_id;
    public $connected_userId;

    /**
     * Create a new event instance.
     */
    public function __construct($data)
    {
        $this->receiver_id = $data['receiver_id'];
        $this->sender_id = $data['sender_id'];
        $this->offer = $data['offer'];
        $this->type = $data['type'];
        $this->connected_userId = $data['connected_userId'];
    }

    public function broadcastWith()
    {
        return [
            'receiverId' => $this->receiver_id,
            'senderId' => $this->sender_id,
            'offer'=>$this->offer,
            'type'=>$this->type,
            'connected_userId'=>$this->connected_userId,
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
            new PrivateChannel('handel-offer-channel'),
        ];
    }
}
