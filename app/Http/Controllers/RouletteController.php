<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouletteController extends Controller
{
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

    public function spinWheel()
    {
        $number = rand(0,14);

        return response()->json(['number' => $number]);
    }

    public function checkBets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activeBets' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $activeBets = $request->input('activeBets', []);
        $winningNumber = $request->input('winningNumber');

        $order = [0, 11, 5, 10, 6, 9, 7, 8, 1, 14, 2, 13, 3, 12, 4];
        $position = $order[$winningNumber];
        $winningColor = $this->getColorByNumber($position);

        foreach ($activeBets as $betId) {
            $bet = Bet::find($betId);

            if (!$bet) {
                continue;
            }

            $user = $bet->user;

            if ($bet->color === $winningColor) {

                if($bet->color === 'green') {
                    $user->balance += $bet->amount * 14;
                }
                else $user->balance += $bet->amount * 2;
                $user->save();
            }
        }

        return response()->json([
            'message' => 'Bet results',
        ]);
    }

    private function getColorByNumber($number)
    {
        if ($number === 0) return 'green';
        return in_array($number, [1, 3, 5, 7, 9, 12, 14]) ? 'red' : 'black';
    }
}
