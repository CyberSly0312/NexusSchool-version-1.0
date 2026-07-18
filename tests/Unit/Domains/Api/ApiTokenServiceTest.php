<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api;

use App\Domains\Api\DTOs\ApiTokenData;
use App\Domains\Api\Events\ApiTokenCreated;
use App\Domains\Api\Events\ApiTokenRevoked;
use App\Domains\Api\Services\ApiTokenService;
use App\Models\ApiClient;
use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ApiTokenService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApiTokenService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_token(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();

        $result = $this->service->create($client, [
            'name' => 'Test Token',
        ]);

        $this->assertInstanceOf(ApiTokenData::class, $result);
        $this->assertNotEmpty($result->token);
        $this->assertEquals('Test Token', $result->name);

        $storedToken = ApiToken::find($result->id);
        $this->assertNotEquals($result->token, $storedToken->token);
    }

    public function test_can_revoke_token(): void
    {
        Event::fake([ApiTokenRevoked::class]);
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $token = ApiToken::factory()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
        ]);

        $this->service->revoke($token);

        $this->assertSoftDeleted('api_tokens', ['id' => $token->id]);
    }

    public function test_can_find_by_hashed_token(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $plainText = Str::random(80);
        $hashedToken = hash('sha256', $plainText);
        $token = ApiToken::factory()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
            'token' => $hashedToken,
            'is_active' => true,
        ]);

        $found = $this->service->findByToken($hashedToken);

        $this->assertNotNull($found);
        $this->assertEquals($token->id, $found->id);
    }

    public function test_validate_token_returns_true_for_valid(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $token = ApiToken::factory()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
            'is_active' => true,
            'expires_at' => now()->addHours(24),
        ]);

        $result = $this->service->validateToken($token);

        $this->assertTrue($result);
    }

    public function test_validate_token_returns_false_for_expired(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $token = ApiToken::factory()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
            'is_active' => true,
            'expires_at' => now()->subHour(),
        ]);

        $result = $this->service->validateToken($token);

        $this->assertFalse($result);
    }

    public function test_validate_token_returns_false_when_inactive(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $token = ApiToken::factory()->inactive()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
        ]);

        $result = $this->service->validateToken($token);

        $this->assertFalse($result);
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([ApiTokenCreated::class]);
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();

        $this->service->create($client, [
            'name' => 'Event Token',
        ]);

        Event::assertDispatched(ApiTokenCreated::class);
    }
}
