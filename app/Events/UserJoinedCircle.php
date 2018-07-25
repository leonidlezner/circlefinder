<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserJoinedCircle
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $circle;
    public $membership;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($circle, $user, $membership)
    {
        $this->circle = $circle;
        $this->user = $user;
        $this->membership = $membership;
    }
}
