<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function assignCoins(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'coins' => 'required|integer|min:1',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $user->coins = $request->coins;
        $user->save();

        return response()->json([
            'message' => 'Coins assigned successfully!',
            'new_coins' => $user->coins
        ]);
    }
}
