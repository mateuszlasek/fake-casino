<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewBetPlaced implements ShouldBroadcast
{
    public $color;
    public $amount;
    public $userId;
    public $username;

    public function __construct($color, $amount, $userId, $username)
    {
        $this->color = $color;
        $this->amount = $amount;
        $this->userId = $userId;
        $this->username = $username;
    }

    public function broadcastOn()
    {
        return new Channel('roulette.bets');
    }
}
