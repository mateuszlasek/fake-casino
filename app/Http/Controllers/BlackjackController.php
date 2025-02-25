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
        $user = auth()->user();
        $bet = $request->bet;

        if (!$this->blackjackService->validateUserBalance($user, $bet)) {
            return response()->json(['error' => 'Not enough balance!'], 400);
        }

        $user->balance -= $bet;
        $user->save();

        $deck = $this->blackjackService->createDeck();
        list($playerCards, $dealerCards) = $this->blackjackService->dealCards($deck);

        Session::put('blackjack', [
            'deck' => $deck,
            'playerCards' => $playerCards,
            'dealerCards' => $dealerCards,
            'bet' => $bet,
            'gameOver' => false
        ]);

        return response()->json([
            'playerCards' => $playerCards,
            'dealerCards' => [$dealerCards[0], 'hidden'],
            'playerScore' => $this->blackjackService->calculateScore($playerCards),
            'dealerScore' => $this->blackjackService->calculateScore([$dealerCards[0]]),
            'balance' => $user->balance
        ]);
    }

    public function stand()
    {
        $game = Session::get('blackjack');
        $dealerCards = $this->blackjackService->dealerPlay($game['deck'], $game['dealerCards']);
        $dealerScore = $this->blackjackService->calculateScore($dealerCards);
        $playerScore = $this->blackjackService->calculateScore($game['playerCards']);

        $result = $this->blackjackService->determineWinner($playerScore, $dealerScore);

        $user = auth()->user();
        $bet = $game['bet'];

        $this->blackjackService->updateUserBalance($user, $bet, $result);

        $game['gameOver'] = true;
        Session::put('blackjack', $game);

        return response()->json([
            'dealerCards' => $dealerCards,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'gameOver' => true,
            'result' => $result,
            'balance' => $user->balance
        ]);
    }

    public function hit()
    {
        $game = Session::get('blackjack');
        $game['playerCards'][] = array_pop($game['deck']);
        $playerScore = $this->blackjackService->calculateScore($game['playerCards']);

        if ($this->blackjackService->checkGameOver($playerScore)) {
            $game['gameOver'] = true;
        }

        Session::put('blackjack', $game);

        return response()->json([
            'playerCards' => $game['playerCards'],
            'playerScore' => $playerScore,
            'gameOver' => $game['gameOver']
        ]);
    }
}
