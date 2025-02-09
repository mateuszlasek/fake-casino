<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class RouletteController extends Controller
{

    public function showRoulettePage()
    {
        $user = auth()->user();
        return Inertia::render('Roulette', [
            'balance' => $user->balance,
        ]);
    }

    public function placeBet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'required|string|in:red,green,black',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();

        if ($user->balance < $request->amount) {
            return response()->json(['error' => 'Not enough balance'], 400);
        }

        $bet = Bet::create([
            'user_id' => $user->id,
            'color' => $request->color,
            'amount' => $request->amount,
        ]);

        $user->balance -= $request->amount;
        $user->save();

        return response()->json([
            'message' => 'Bet placed',
            'bet_id' => $bet->id,
            'new_balance' => $user->balance,
        ], 200);
    }

    public function spinWheel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activeBets' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $activeBets = $request->input('activeBets', []);

        $winningNumber = rand(0, 14);
        $winningColor = $this->getColorByNumber($winningNumber);

        $bets = Bet::whereIn('id', $activeBets)->get();

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

        return response()->json(['number' => $winningNumber]);
    }

    private function getColorByNumber($number)
    {
        if ($number === 0) return 'green';
        return in_array($number, [1, 2, 3, 4, 5, 6, 7]) ? 'red' : 'black';
    }
}
