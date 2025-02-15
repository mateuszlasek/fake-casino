<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RouletteSpinEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $winningNumber;
    public $randomize;

    /**
     * Create a new event instance.
     *
     * @param int $winningNumber
     */
    public function __construct(int $winningNumber, int $randomize)
    {
        $this->winningNumber = $winningNumber;
        $this->randomize = $randomize;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('roulette');
    }
}
