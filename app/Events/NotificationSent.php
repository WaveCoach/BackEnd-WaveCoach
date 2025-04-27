<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $title;
    public $userId;

    public function __construct($message, $title, $userId)
    {
        $this->message = $message;
        $this->title = $title;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        // Broadcast ke channel private untuk user yang dituju
        return new Channel('notification-channel-user-' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'title'   => $this->title,
        ];
    }
}
