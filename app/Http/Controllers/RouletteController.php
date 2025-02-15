<?php

namespace App\Http\Controllers;

use App\Events\BetPlacedEvent;
use App\Events\RouletteSpinEvent;
use App\Services\RouletteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class RouletteController extends Controller
{
    protected $rouletteService;

    public function __construct(RouletteService $rouletteService)
    {
        $this->rouletteService = $rouletteService;
    }

    public function showRoulettePage()
    {
        $user = auth()->user();
        $activeBets = $this->rouletteService->getActiveBets();

        return Inertia::render('Roulette', [
            'balance' => $user->balance,
            'user'    => $user,
            'initialBets' => $activeBets
        ]);
    }

    public function placeBet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color'  => 'required|string|in:red,green,black',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();

        try {
            $bet = $this->rouletteService->placeBet($user, $request->input('color'), (float)$request->input('amount'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        event(new BetPlacedEvent($user->name, $bet->color, $bet->amount));

        return response()->json([
            'message'     => 'Bet placed',
            'bet_id'      => $bet->id,
            'new_balance' => $user->balance,
        ], 200);
    }

    public function spinWheel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activeBets' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $activeBets = $request->input('activeBets', []);
        $winningNumber = $this->rouletteService->spinWheel($activeBets);

        event(new RouletteSpinEvent($winningNumber, rand(-37, 37)));

        return response()->json(['number' => $winningNumber]);
    }
}
