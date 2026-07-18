<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Ai;

use App\Domains\Ai\DTOs\AiUsageLogData;
use App\Domains\Ai\Services\AiService;
use App\Models\AiUsageLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AiServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AiService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AiService::class);

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_record_usage(): void
    {
        $result = $this->service->recordUsage([
            'school_id' => $this->schoolId,
            'action' => 'message_generated',
            'model' => 'gpt-4o',
            'input_tokens' => 100,
            'output_tokens' => 50,
        ]);

        $this->assertInstanceOf(AiUsageLogData::class, $result);
        $this->assertEquals('message_generated', $result->action);
        $this->assertEquals(150, $result->totalTokens);
    }

    public function test_can_get_usage_stats(): void
    {
        AiUsageLog::factory()->forSchool($this->schoolId)->count(5)->create();
        $stats = $this->service->getUsageStats($this->schoolId);
        $this->assertEquals(5, $stats['total_requests']);
        $this->assertArrayHasKey('total_tokens', $stats);
        $this->assertArrayHasKey('total_cost', $stats);
    }

    public function test_can_get_daily_usage(): void
    {
        AiUsageLog::factory()->forSchool($this->schoolId)->count(3)->create();
        $usage = $this->service->dailyUsage($this->schoolId);
        $this->assertGreaterThan(0, $usage);
    }

    public function test_can_get_monthly_usage(): void
    {
        AiUsageLog::factory()->forSchool($this->schoolId)->count(3)->create();
        $usage = $this->service->monthlyUsage($this->schoolId);
        $this->assertGreaterThan(0, $usage);
    }

    public function test_can_access_sub_services(): void
    {
        $this->assertNotNull($this->service->getProviders());
        $this->assertNotNull($this->service->getConversations());
        $this->assertNotNull($this->service->getPrompts());
        $this->assertNotNull($this->service->getKnowledge());
    }
}
