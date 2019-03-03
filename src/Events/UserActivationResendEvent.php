<?php

namespace Omadonex\LaravelSupport\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserActivationResendEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $userActivation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $userActivation)
    {
        $this->user = $user;
        $this->userActivation = $userActivation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
