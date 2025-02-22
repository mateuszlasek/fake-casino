<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\RouletteHistory;
use App\Models\RouletteState;
use App\Models\User;
use Exception;

class RouletteService
{
    public function placeBet(User $user, string $color, float $amount): Bet
    {
        if ($user->balance < $amount) {
            throw new Exception('Not enough balance');
        }

        $bet = Bet::create([
            'user_id' => $user->id,
            'color'   => $color,
            'amount'  => $amount,
        ]);

        $user->decrement('balance', $amount);

        return $bet;
    }

    public function initiateSpin(): array
    {
        $winningNumber = rand(0, 14);
        $randomize = rand(-37, 37);
        $startTime = now();
        $duration = 30000;

        RouletteState::updateOrCreate(
            ['id' => 1],
            [
                'spinning'       => true,
                'winning_number' => $winningNumber,
                'randomize'      => $randomize,
                'start_time'     => $startTime,
            ]
        );

        $this->processBets($winningNumber);

        return [
            'winningNumber' => $winningNumber,
            'randomize'     => $randomize,
            'startTime'     => $startTime,
            'duration'      => $duration,
        ];
    }

    private function processBets(int $winningNumber): void
    {
        $winningColor = $this->getColorByNumber($winningNumber);

        RouletteHistory::create(['color' => $winningColor]);
        $historyCount = RouletteHistory::count();
        if ($historyCount > 10) {
            RouletteHistory::oldest()->limit($historyCount - 10)->delete();
        }

        $activeBets = Bet::where('active', true)->get();
        foreach ($activeBets as $bet) {
            $user = $bet->user;
            if ($bet->color === $winningColor) {
                $multiplier = ($winningColor === 'green') ? 14 : 2;
                $user->increment('balance', $bet->amount * $multiplier);
            }
        }
    }

    private function getColorByNumber(int $number): string
    {
        if ($number === 0) {
            return 'green';
        }

        return in_array($number, [1, 2, 3, 4, 5, 6, 7]) ? 'red' : 'black';
    }

    public function getActiveBets(): array
    {
        return Bet::where('active', true)
            ->with('user')
            ->get()
            ->groupBy('color')
            ->map(function ($bets) {
                return $bets->map(function ($bet) {
                    return [
                        'username' => $bet->user->name,
                        'amount'   => $bet->amount,
                    ];
                });
            })
            ->toArray();
    }

    public function getCurrentState(): array
    {
        $state = RouletteState::first();
        return [
            'spinning'      => $state ? $state->spinning : false,
            'winningNumber' => $state->winning_number ?? null,
            'randomize'     => $state->randomize ?? null,
            'startTime'     => $state->start_time ?? null,
        ];
    }

    public function clearSpin(): void
    {
        RouletteState::where('id', 1)->update(['spinning' => false]);
        Bet::where('active', true)->update(['active' => false]);
    }

    public function getHistory(): array
    {
        return RouletteHistory::latest()->take(10)->pluck('color')->toArray();
    }
}
