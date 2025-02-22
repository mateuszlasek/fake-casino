<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RouletteSpinEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public int $winningNumber;
    public int $randomize;
    public $startTime;
    public $history;


    /**
     * Create a new event instance.
     *
     * @param int $winningNumber
     */
    public function __construct(int $winningNumber, int $randomize, $startTime, $history)
    {
        $this->winningNumber = $winningNumber;
        $this->randomize = $randomize;
        $this->startTime = $startTime;
        $this->history = $history;
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
