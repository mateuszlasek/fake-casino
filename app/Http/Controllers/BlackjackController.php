<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\BlackjackService;

class BlackjackController extends Controller
{
    protected $blackjackService;

    public function __construct(BlackjackService $blackjackService)
    {
        $this->blackjackService = $blackjackService;
    }

    public function startGame(Request $request)
    {
        $deck = $this->blackjackService->createDeck();

        $playerCards = [array_pop($deck), array_pop($deck)];
        $dealerCards = [array_pop($deck), array_pop($deck)];

        Session::put('blackjack', [
            'deck' => $deck,
            'playerCards' => $playerCards,
            'dealerCards' => $dealerCards,
            'bet' => $request->bet,
            'gameOver' => false
        ]);

        return response()->json([
            'playerCards' => $playerCards,
            'dealerCards' => [$dealerCards[0], 'hidden'],
            'playerScore' => $this->blackjackService->calculateScore($playerCards),
            'dealerScore' => $this->blackjackService->calculateScore([$dealerCards[0]]),
        ]);
    }

    public function hit()
    {
        $game = Session::get('blackjack');
        $game['playerCards'][] = array_pop($game['deck']);
        $playerScore = $this->blackjackService->calculateScore($game['playerCards']);

        if ($playerScore > 21) {
            $game['gameOver'] = true;
        }

        Session::put('blackjack', $game);

        return response()->json([
            'playerCards' => $game['playerCards'],
            'playerScore' => $playerScore,
            'gameOver' => $game['gameOver']
        ]);
    }

    public function stand()
    {
        $game = Session::get('blackjack');
        $dealerScore = $this->blackjackService->calculateScore($game['dealerCards']);

        while ($dealerScore < 17) {
            $game['dealerCards'][] = array_pop($game['deck']);
            $dealerScore = $this->blackjackService->calculateScore($game['dealerCards']);
        }

        $playerScore = $this->blackjackService->calculateScore($game['playerCards']);
        $game['gameOver'] = true;
        Session::put('blackjack', $game);

        return response()->json([
            'dealerCards' => $game['dealerCards'],
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'gameOver' => true,
            'result' => $this->blackjackService->determineWinner($playerScore, $dealerScore)
        ]);
    }
}
