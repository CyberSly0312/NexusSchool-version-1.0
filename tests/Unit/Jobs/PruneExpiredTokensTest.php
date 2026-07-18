<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Domains\Api\Services\ApiTokenService;
use App\Jobs\PruneExpiredTokens;
use App\Models\ApiClient;
use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PruneExpiredTokensTest extends TestCase
{
    use RefreshDatabase;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_prunes_expired_tokens(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $token = ApiToken::factory()->expired()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
            'is_active' => true,
        ]);

        $job = new PruneExpiredTokens;
        $job->handle(app(ApiTokenService::class));

        $this->assertSoftDeleted('api_tokens', ['id' => $token->id]);
    }

    public function test_preserves_active_tokens(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $token = ApiToken::factory()->create([
            'api_client_id' => $client->id,
            'school_id' => $this->schoolId,
            'is_active' => true,
            'expires_at' => now()->addHours(24),
        ]);

        $job = new PruneExpiredTokens;
        $job->handle(app(ApiTokenService::class));

        $this->assertDatabaseHas('api_tokens', ['id' => $token->id, 'is_active' => true]);
    }
}
