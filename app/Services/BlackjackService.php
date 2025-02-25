<?php

namespace App\Services;

class BlackjackService
{
    private $suits = ['♥', '♦', '♣', '♠'];
    private $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

    public function createDeck(): array
    {
        $deck = [];

        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                $deck[] = $value . $suit;
            }
        }

        shuffle($deck);
        return $deck;
    }

    public function calculateScore(array $cards): int
    {
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

    public function determineWinner(int $playerScore, int $dealerScore): string
    {
        if ($playerScore > 21) return 'dealer';
        if ($dealerScore > 21) return 'player';
        if ($playerScore === $dealerScore) return 'push';
        return ($playerScore > $dealerScore) ? 'player' : 'dealer';
    }
}
