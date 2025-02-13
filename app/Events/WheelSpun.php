<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WheelSpun implements ShouldBroadcast
{
    public $winningNumber;

    public function __construct($winningNumber)
    {
        $this->winningNumber = $winningNumber;
    }

    public function broadcastOn()
    {
        return new Channel('roulette.spins');
    }
}
