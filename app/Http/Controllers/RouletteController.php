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
            'bet' => $bet,
            'new_balance' => $user->balance,
        ], 200);
    }

    public function spinWheel()
    {
        $number = rand(0,14);

        return response()->json(['number' => $number]);
    }
}
