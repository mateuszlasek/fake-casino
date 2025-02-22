<?php

namespace App\Http\Controllers;

use App\Events\BetPlacedEvent;
use App\Events\TimerStartedEvent;
use App\Http\Requests\PlaceBetRequest;
use App\Jobs\CompleteRouletteSpinJob;
use App\Models\RouletteState;
use App\Services\RouletteService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class RouletteController extends Controller
{
    private RouletteService $rouletteService;

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

    public function placeBet(PlaceBetRequest $request): JsonResponse
    {
        $user = $request->user();

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

    public function spinWheel(): JsonResponse
    {
        $state = $this->rouletteService->initiateSpin();

        event(new TimerStartedEvent($state['startTime'], $state['duration']));

        dispatch(new CompleteRouletteSpinJob($state['winningNumber'], $state['randomize'], $state['startTime']))
            ->delay(now()->addSeconds(30));

        return response()->json(['number' => $state['winningNumber']]);
    }

    public function getCurrentSpin(): JsonResponse
    {
        $state = $this->rouletteService->getCurrentState();

        return response()->json($state);
    }

    public function clearSpin(): JsonResponse
    {
        $this->rouletteService->clearSpin();
        return response()->json(['message' => 'Spin cleared']);
    }

    public function getHistory(): JsonResponse
    {
        $history = $this->rouletteService->getHistory();
        return response()->json($history);
    }
}
