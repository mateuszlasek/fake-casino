<?php

namespace App\Services;

use App\Models\Bet;
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

    public function spinWheel(array $activeBetIds): int
    {
        $winningNumber = rand(0, 14);
        $winningColor = $this->getColorByNumber($winningNumber);

        if (!empty($activeBetIds)) {
            $bets = Bet::whereIn('id', $activeBetIds)->get();

            foreach ($bets as $bet) {
                $user = $bet->user;

                if ($bet->color === $winningColor) {
                    if ($bet->color === 'green') {
                        $user->balance += $bet->amount * 14;
                    } else {
                        $user->balance += $bet->amount * 2;
                    }

                    $user->save();
                }
            }
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
}
