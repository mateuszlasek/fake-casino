<?php

namespace Services;

use App\Models\User;
use App\Services\SlotService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotServiceTest extends TestCase
{
    use RefreshDatabase;

    private SlotService $slotService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->slotService = new SlotService();
    }

    public function test_spin_throws_exception_when_insufficient_balance(): void
    {
        $user = User::factory()->create(['balance' => 5]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough balance');

        $this->slotService->spin($user, 10);
    }

    public function test_spin_deducts_bet_on_loss(): void
    {
        $user = User::factory()->create(['balance' => 100]);
        $this->forceLoss();

        $result = $this->slotService->spin($user, 10);

        $this->assertEquals(90.0, $result['balance']);
        $this->assertEquals(0.0, $result['prize']);
        $this->assertFalse($result['is_win']);
    }

    public function test_spin_adds_prize_on_win(): void
    {
        $user = User::factory()->create(['balance' => 100]);
        $this->forceWin();

        $result = $this->slotService->spin($user, 10);

        $this->assertEquals(190.0, $result['balance']);
        $this->assertEquals(100.0, $result['prize']);
        $this->assertTrue($result['is_win']);
    }

    public function test_spin_returns_correct_structure(): void
    {
        $user = User::factory()->create(['balance' => 100]);

        $result = $this->slotService->spin($user, 10);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('balance', $result);
        $this->assertArrayHasKey('prize', $result);
        $this->assertArrayHasKey('is_win', $result);
        $this->assertCount(3, $result['result']);
    }

    private function forceWin(): void
    {
        $reflection = new \ReflectionClass($this->slotService);
        $property = $reflection->getProperty('symbols');
        $property->setAccessible(true);
        $property->setValue($this->slotService, ['💎', '💎', '💎']);
    }

    private function forceLoss(): void
    {
        $reflection = new \ReflectionClass($this->slotService);
        $property = $reflection->getProperty('symbols');
        $property->setAccessible(true);
        $property->setValue($this->slotService, ['🍒', '🍊', '🍋', '🍇', '7️⃣', '🔔', '💰']);
    }
}
