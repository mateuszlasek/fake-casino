<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SlotController extends Controller
{
    public function index()
    {
        return Inertia::render('Slots', [
            'balance' => Auth::user()->balance
        ]);
    }

    public function spin(Request $request)
    {
        $user = Auth::user();
        $bet = $request->validate(['bet' => 'required|numeric|min:1'])['bet'];

        if ($user->balance < $bet) {
            return response()->json(['error' => 'Niewystarczające środki'], 400);
        }

        $symbols = ['🍒', '🍊', '🍋', '🍇', '💎', '7️⃣', '🔔', '💰'];
        $result = array_map(fn() => $symbols[array_rand($symbols)], range(1, 3));

        $user->balance -= $bet;

        $isWin = count(array_unique($result)) === 1;
        $prize = $isWin ? $bet * 10 : 0;

        if ($isWin) {
            $user->balance += $prize;
        }

        $user->save();

        return response()->json([
            'result' => $result,
            'balance' => $user->balance,
            'prize' => $prize,
            'is_win' => $isWin
        ]);
    }
}
