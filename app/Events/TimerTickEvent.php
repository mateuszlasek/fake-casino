<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerTickEvent implements ShouldBroadcast
{
    public $timeLeft;

    public function __construct($timeLeft)
    {
        $this->timeLeft = $timeLeft;
    }

    public function broadcastOn()
    {
        return new Channel('roulette');
    }
}
