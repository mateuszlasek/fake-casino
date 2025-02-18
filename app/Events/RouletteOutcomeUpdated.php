<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RouletteOutcomeUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $color;
    public $created_at;

    public function __construct(string $color)
    {
        $this->color = $color;
        $this->created_at = now()->toDateTimeString();
    }

    public function broadcastOn()
    {
        return new Channel('roulette');
    }
}
