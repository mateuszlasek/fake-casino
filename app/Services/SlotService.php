<?php

namespace App\Services;

use App\Models\User;
use Mockery\Exception;

class SlotService
{
    private $symbols = ['🍒', '🍊', '🍋', '🍇', '💎', '7️⃣', '🔔', '💰'];


    public function spin(User $user, float $bet): array
    {
        if ($user->balance < $bet) {
            throw new Exception('Not enough balance', 400);
        }

        $user->balance -= $bet;

        $result = [];
        for ($i = 0; $i < 3; $i++) {
            $result[] = $this->symbols[array_rand($this->symbols)];
        }

        $isWin = count(array_unique($result)) === 1;

        $prize = 0;
        if ($isWin) {
            $prize = $bet * 10;
            $user->balance += $prize;
        }

        $user->save();

        return [
            'result' => $result,
            'balance' => $user->balance,
            'prize' => $prize,
            'is_win' => $isWin
        ];
    }
}
