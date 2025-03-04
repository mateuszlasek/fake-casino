<?php

namespace Services;

use App\Models\Bet;
use App\Models\RouletteHistory;
use App\Models\RouletteState;
use App\Models\User;
use App\Services\RouletteService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouletteServiceTest extends TestCase
{
    use RefreshDatabase;

    private RouletteService $rouletteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rouletteService = new RouletteService();
    }

    public function test_place_bet_throws_exception_when_insufficient_balance(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 10
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough balance');

        $this->rouletteService->placeBet($user, 'red', 20);
    }

    public function test_place_bet_decrements_user_balance(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100
        ]);

        $this->rouletteService->placeBet($user, 'red', 50);

        $this->assertEquals(50, $user->fresh()->balance);
    }

    public function test_initiate_spin_updates_roulette_state(): void
    {
        $this->rouletteService->initiateSpin();
        $state = RouletteState::first();

        $this->assertEquals(1, $state->id);
        $this->assertTrue((bool)$state->spinning);
        $this->assertNotNull($state->winning_number);
    }

    public function test_process_bets_pays_correct_green_multiplier(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100
        ]);

        $user->decrement('balance', 10);
        Bet::create([
            'user_id' => $user->id,
            'color' => 'green',
            'amount' => 10,
            'active' => true
        ]);

        $this->invokePrivateMethod($this->rouletteService, 'processBets', [0]);

        $this->assertEquals(230, $user->fresh()->balance);
    }

    public function test_process_bets_trims_history_to_ten_entries(): void
    {
        foreach (range(1, 11) as $i) {
            RouletteHistory::create(['color' => 'red']);
        }

        $this->invokePrivateMethod($this->rouletteService, 'processBets', [0]);

        $this->assertCount(10, RouletteHistory::all());
    }

    public function test_get_current_state_returns_correct_values(): void
    {
        RouletteState::create([
            'id' => 1,
            'spinning' => 1,
            'winning_number' => 5,
            'randomize' => 10,
            'start_time' => now()
        ]);

        $state = $this->rouletteService->getCurrentState();

        $this->assertTrue((bool)$state['spinning']);
        $this->assertEquals(5, $state['winningNumber']);
    }

    public function test_clear_spin_deactivates_bets_and_updates_state(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'balance' => 100
        ]);

        RouletteState::create([
            'id' => 1,
            'spinning' => 1
        ]);

        Bet::create([
            'user_id' => $user->id,
            'color' => 'red',
            'amount' => 10,
            'active' => 1
        ]);

        $this->rouletteService->clearSpin();

        $this->assertFalse((bool)RouletteState::first()->spinning);
        $this->assertFalse((bool)Bet::first()->active);
    }

    private function invokePrivateMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
