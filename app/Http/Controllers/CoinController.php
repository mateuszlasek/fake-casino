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

        $user->coins += $request->coins;

        return response()->json(['message' => 'Coins assigned successfully!']);
    }
}
