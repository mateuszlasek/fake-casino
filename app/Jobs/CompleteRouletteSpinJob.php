<?php

namespace App\Jobs;

use App\Events\RouletteSpinEvent;
use App\Models\RouletteHistory;
use App\Models\RouletteState;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompleteRouletteSpinJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $winningNumber;
    protected $randomize;
    protected $startTime;

    public function __construct($winningNumber, $randomize, $startTime)
    {
        $this->winningNumber = $winningNumber;
        $this->randomize = $randomize;
        $this->startTime = $startTime;
    }

    public function handle()
    {
        event(new RouletteSpinEvent($this->winningNumber, $this->randomize, $this->startTime, $this->getHistory()));
        RouletteState::where('id', 1)->update(['spinning' => false]);
    }

    protected function getHistory()
    {
        return RouletteHistory::latest()->take(10)->pluck('color');
    }
}
