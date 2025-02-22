<?php

namespace App\Jobs;

use App\Events\TimerTickEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TimerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $duration;

    public function __construct($duration)
    {
        $this->duration = $duration;
    }

    public function handle()
    {
        for ($i = 0; $i < $this->duration; $i++) {
            $remainingTime = $this->duration - $i;
            event(new TimerTickEvent($remainingTime * 1000));
            sleep(1);
        }
    }
}
