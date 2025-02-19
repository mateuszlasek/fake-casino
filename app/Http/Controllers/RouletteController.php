<?php

namespace App\Http\Controllers;

use App\Events\BetPlacedEvent;
use App\Events\RouletteSpinEvent;
use App\Events\TimerTickEvent;
use App\Models\RouletteHistory;
use App\Models\RouletteState;
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
            'balance'     => $user->balance,
            'user'        => $user,
            'initialBets' => $activeBets,
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
            $bet = $this->rouletteService->placeBet($user, $request->input('color'), (float) $request->input('amount'));
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

    public function spinWheel()
    {
        $winningNumber = $this->rouletteService->spinWheel();
        $startTime = now();
        $randomize = rand(-37, 37);

        RouletteState::updateOrCreate(
            ['id' => 1],
            [
                'spinning'       => true,
                'winning_number' => $winningNumber,
                'randomize'      => $randomize,
                'start_time'     => $startTime,
            ]
        );

        // Czas trwania timera: 30 sekund
        $duration = 30;
        $remainingTime = $duration;

        // Emitujemy pierwszy tick (czas w ms)
        event(new TimerTickEvent($remainingTime * 1000));

        // Pętla odliczająca – co sekundę wysyłamy tick z pozostałym czasem
        for ($i = 1; $i < $duration; $i++) {
            $remainingTime = $duration - $i;
            event(new TimerTickEvent($remainingTime * 1000));
            sleep(1);
        }

        event(new RouletteSpinEvent($winningNumber, $randomize, $startTime, $this->getHistory()));

        return response()->json(['number' => $winningNumber]);
    }

    public function getCurrentSpin()
    {
        $state = RouletteState::first();

        return response()->json([
            'spinning'      => $state ? $state->spinning : false,
            'winningNumber' => $state ? $state->winning_number : null,
            'randomize'     => $state ? $state->randomize : null,
            'startTime'     => $state ? $state->start_time : null,
        ]);
    }

    public function clearSpin()
    {
        RouletteState::where('id', 1)->update(['spinning' => false]);
    }

    public function getHistory()
    {
        $history = RouletteHistory::latest()->take(10)->pluck('color');
        return response()->json($history);
    }

    public function startTimer()
    {
        $duration = 30;
        $remainingTime = $duration;

        event(new TimerTickEvent($remainingTime * 1000));

        for ($i = 0; $i < $duration; $i++) {
            $remainingTime = $duration - $i;
            event(new TimerTickEvent($remainingTime * 1000));
            sleep(1);
        }

        $this->spinWheel();
    }
}
