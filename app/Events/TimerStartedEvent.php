<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimerStartedEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $startTime;
    public $duration; // w milisekundach

    public function __construct($startTime, $duration)
    {
        $this->startTime = $startTime;
        $this->duration = $duration;
    }

    public function broadcastOn()
    {
        return new Channel('roulette');
    }
}
