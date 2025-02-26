<?php

namespace Tests\Unit;

use App\Models\Bet;
use App\Models\RouletteHistory;
use App\Models\RouletteState;
use App\Models\User;
use App\Services\RouletteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class RouletteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RouletteService $rouletteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rouletteService = new RouletteService();
    }

    public function testPlaceBetWithSufficientBalance()
    {
        $user = User::factory()->create(['balance' => 100]);

        $bet = $this->rouletteService->placeBet($user, 'red', 50);

        $this->assertDatabaseHas('bets', [
            'id'      => $bet->id,
            'user_id' => $user->id,
            'color'   => 'red',
            'amount'  => 50,
        ]);

        $user->refresh();
        $this->assertEquals(50, $user->balance);
    }

    public function testPlaceBetWithInsufficientBalance()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough balance');

        $user = User::factory()->create(['balance' => 30]);
        $this->rouletteService->placeBet($user, 'black', 50);
    }

    public function testInitiateSpinCreatesStateAndProcessesBets()
    {
        $user = User::factory()->create(['balance' => 100]);
        $bet = Bet::factory()->create([
            'user_id' => $user->id,
            'color'   => 'red',
            'amount'  => 20,
            'active'  => true,
        ]);

        $state = $this->rouletteService->initiateSpin();

        $this->assertDatabaseHas('roulette_states', [
            'id'             => 1,
            'spinning'       => true,
            'winning_number' => $state['winningNumber'],
        ]);

        $winningColor = ($state['winningNumber'] === 0)
            ? 'green'
            : (in_array($state['winningNumber'], [1,2,3,4,5,6,7]) ? 'red' : 'black');

        $this->assertDatabaseHas('roulette_histories', [
            'color' => $winningColor,
        ]);

        $user->refresh();
        if ($bet->color === $winningColor) {
            $multiplier = ($winningColor === 'green') ? 14 : 2;
            $expectedBalance = 100 + (20 * $multiplier);
        } else {
            $expectedBalance = 100;
        }
        $this->assertEquals($expectedBalance, $user->balance);
    }

    public function testGetActiveBets()
    {
        $user1 = User::factory()->create(['balance' => 100]);
        $user2 = User::factory()->create(['balance' => 100]);

        Bet::factory()->create([
            'user_id' => $user1->id,
            'color'   => 'red',
            'amount'  => 10,
            'active'  => true,
        ]);

        Bet::factory()->create([
            'user_id' => $user2->id,
            'color'   => 'black',
            'amount'  => 15,
            'active'  => true,
        ]);

        $activeBets = $this->rouletteService->getActiveBets();

        $this->assertArrayHasKey('red', $activeBets);
        $this->assertArrayHasKey('black', $activeBets);
        $this->assertCount(1, $activeBets['red']);
        $this->assertCount(1, $activeBets['black']);
        $this->assertEquals($user1->name, $activeBets['red'][0]['username']);
        $this->assertEquals(10, $activeBets['red'][0]['amount']);
    }

    public function testGetCurrentState()
    {
        $state = $this->rouletteService->getCurrentState();
        $this->assertFalse($state['spinning']);
        $this->assertNull($state['winningNumber']);
        $this->assertNull($state['randomize']);
        $this->assertNull($state['startTime']);

        RouletteState::create([
            'spinning'       => true,
            'winning_number' => 5,
            'randomize'      => 10,
            'start_time'     => now(),
        ]);

        $state = $this->rouletteService->getCurrentState();
        $this->assertTrue($state['spinning']);
        $this->assertEquals(5, $state['winningNumber']);
        $this->assertEquals(10, $state['randomize']);
        $this->assertNotNull($state['startTime']);
    }

    public function testClearSpin()
    {
        RouletteState::create([
            'spinning'       => true,
            'winning_number' => 3,
            'randomize'      => 5,
            'start_time'     => now(),
        ]);

        $bet = Bet::factory()->create([
            'active' => true,
        ]);

        $this->rouletteService->clearSpin();

        $this->assertDatabaseMissing('roulette_states', [
            'id'       => 1,
            'spinning' => true,
        ]);

        $this->assertDatabaseMissing('bets', [
            'id'     => $bet->id,
            'active' => true,
        ]);
    }

    public function testGetHistory()
    {
        for ($i = 1; $i <= 12; $i++) {
            RouletteHistory::create(['color' => 'red']);
        }

        $history = $this->rouletteService->getHistory();
        $this->assertCount(10, $history);
    }
}
