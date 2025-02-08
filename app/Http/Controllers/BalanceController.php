<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function assignBalance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'balance' => 'required|integer|min:1',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }

        $user->balance = $request->balance;
        $user->save();

        return response()->json([
            'message' => 'Balance assigned successfully!',
            'new_balance' => $user->balance
        ]);
    }

    public function getBalance(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found!'], 404);
        }
        return response()->json([
            'balance' => $user->balance
        ]);
    }
}
