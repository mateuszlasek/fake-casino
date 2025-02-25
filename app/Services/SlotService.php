<?php

namespace App\Services;

use App\Models\User;

class SlotService
{
    private $symbols = ['🍒', '🍊', '🍋', '🍇', '💎', '7️⃣', '🔔', '💰'];

    public function spin(User $user, float $bet): array
    {
        if ($user->balance < $bet) {
            throw new \Exception('Not enough balance', 400);
        }

        $result = array_map(fn() => $this->symbols[array_rand($this->symbols)], range(1, 3));

        $user->balance -= $bet;

        $isWin = count(array_unique($result)) === 1;
        $prize = $isWin ? $bet * 10 : 0;

        if ($isWin) {
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
