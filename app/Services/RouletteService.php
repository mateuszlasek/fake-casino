<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\RouletteHistory;
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

        $user->balance -= $amount;
        $user->save();

        return $bet;
    }

    public function spinWheel(): int
    {
        $winningNumber = rand(0, 14);
        $winningColor = $this->getColorByNumber($winningNumber);

        $activeBets = Bet::where('active', true)->get();

        RouletteHistory::create(['color' => $winningColor]);
        $historyCount = RouletteHistory::count();
        if ($historyCount > 10) {
            RouletteHistory::oldest()->limit($historyCount - 10)->delete();
        }

        foreach ($activeBets as $bet) {
            $user = $bet->user;

            if ($bet->color === $winningColor) {
                $multiplier = ($bet->color === 'green') ? 14 : 2;
                $user->balance += $bet->amount * $multiplier;
                $user->save();
            }

            $bet->update(['active' => false]);
        }

        return $winningNumber;
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
            ->map(function ($bets, $color) {
                return $bets->map(function ($bet) {
                    return [
                        'username' => $bet->user->name,
                        'amount'   => $bet->amount,
                    ];
                });
            })
            ->toArray();
    }
}
