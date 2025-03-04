<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\BlackjackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlackjackServiceTest extends TestCase
{
    use RefreshDatabase;

    private BlackjackService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BlackjackService();
    }

    public function test_create_deck_generates_52_unique_cards(): void
    {
        $deck = $this->service->createDeck();

        $this->assertCount(52, $deck);
        $this->assertCount(52, array_unique($deck));
        $this->assertContains('A♥', $deck);
        $this->assertContains('10♠', $deck);
    }

    public function test_deal_cards_removes_4_cards(): void
    {
        $deck = $this->service->createDeck();
        $originalDeckSize = count($deck);

        list($playerCards, $dealerCards) = $this->service->dealCards($deck);

        $this->assertCount(2, $playerCards);
        $this->assertCount(2, $dealerCards);
        $this->assertCount($originalDeckSize - 4, $deck);
    }

    public function test_calculate_score_with_aces(): void
    {
        $this->assertEquals(21, $this->service->calculateScore(['A♥', 'J♦']));
        $this->assertEquals(12, $this->service->calculateScore(['A♥', '5♦', '6♣']));
        $this->assertEquals(13, $this->service->calculateScore(['A♥', 'A♦', 'A♣']));
    }

    public function test_determine_winner(): void
    {
        $this->assertEquals('dealer', $this->service->determineWinner(23, 20));
        $this->assertEquals('player', $this->service->determineWinner(20, 22));
        $this->assertEquals('push', $this->service->determineWinner(20, 20));
        $this->assertEquals('player', $this->service->determineWinner(19, 18));
    }

    public function test_update_user_balance(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100
        ]);

        // Test win
        $this->service->updateUserBalance($user, 40, 'player');
        $this->assertEquals(200, $user->fresh()->balance);

        // Test push
        $this->service->updateUserBalance($user, 40, 'push');
        $this->assertEquals(240, $user->fresh()->balance);

        // Test loss
        $this->service->updateUserBalance($user, 40, 'dealer');
        $this->assertEquals(240, $user->fresh()->balance);
    }

    public function test_dealer_play_logic(): void
    {
        $deck = ['5♠', 'Q♦'];
        $dealerCards = ['Q♦', '5♦'];

        $result = $this->service->dealerPlay($deck, $dealerCards);
        $this->assertEquals(20, $this->service->calculateScore($result));
    }

    public function test_game_over_check(): void
    {
        $this->assertTrue($this->service->checkGameOver(22));
        $this->assertFalse($this->service->checkGameOver(20));
    }

    public function test_balance_validation(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100
        ]);

        $this->assertTrue($this->service->validateUserBalance($user, 50));
        $this->assertFalse($this->service->validateUserBalance($user, 150));
    }
}
