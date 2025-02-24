<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BlackjackController extends Controller
{
    private function createDeck() {
        $suits = ['♥', '♦', '♣', '♠'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        $deck = [];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = $value.$suit;
            }
        }

        shuffle($deck);
        return $deck;
    }

    private function calculateScore($cards) {
        $score = 0;
        $aces = 0;

        foreach ($cards as $card) {
            $value = preg_replace('/[^0-9AJQK]/', '', $card);

            if ($value === 'A') {
                $aces++;
                $score += 11;
            } elseif (in_array($value, ['J', 'Q', 'K'])) {
                $score += 10;
            } else {
                $score += (int)$value;
            }
        }

        while ($score > 21 && $aces > 0) {
            $score -= 10;
            $aces--;
        }

        return $score;
    }

    public function startGame(Request $request) {
        $deck = $this->createDeck();

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
            'playerScore' => $this->calculateScore($playerCards),
            'dealerScore' => $this->calculateScore([$dealerCards[0]]),
        ]);
    }

    public function hit() {
        $game = Session::get('blackjack');
        $game['playerCards'][] = array_pop($game['deck']);
        $playerScore = $this->calculateScore($game['playerCards']);

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

    public function stand() {
        $game = Session::get('blackjack');
        $dealerScore = $this->calculateScore($game['dealerCards']);

        while ($dealerScore < 17) {
            $game['dealerCards'][] = array_pop($game['deck']);
            $dealerScore = $this->calculateScore($game['dealerCards']);
        }

        $playerScore = $this->calculateScore($game['playerCards']);
        $game['gameOver'] = true;
        Session::put('blackjack', $game);

        return response()->json([
            'dealerCards' => $game['dealerCards'],
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'gameOver' => true,
            'result' => $this->determineWinner($playerScore, $dealerScore)
        ]);
    }

    private function determineWinner($playerScore, $dealerScore) {
        if ($playerScore > 21) return 'dealer';
        if ($dealerScore > 21) return 'player';
        if ($playerScore === $dealerScore) return 'push';
        return ($playerScore > $dealerScore) ? 'player' : 'dealer';
    }
}
