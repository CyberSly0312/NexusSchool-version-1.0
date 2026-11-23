<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Domains\School\Models\School;
use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class OAuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected ApiClient $client;

    protected string $plainTextToken;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('tenants')->insert([
            'id' => '1', 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->client = ApiClient::factory()->create(['school_id' => '1']);
    }

    private function authRequest(string $method, string $uri, array $data = [])
    {
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->plainTextToken];

        if (in_array($method, ['getJson'])) {
            return $this->$method($uri, $headers);
        }

        return $this->$method($uri, $data, $headers);
    }

    public function test_can_obtain_token_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/oauth/token', [
            'client_id' => $this->client->slug,
            'client_secret' => $this->client->secret,
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'expires_at', 'token_type']);
    }

    public function test_returns_401_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/oauth/token', [
            'client_id' => $this->client->slug,
            'client_secret' => $this->client->secret,
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertUnprocessable();
    }

    public function test_can_revoke_token(): void
    {
        $user = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $this->plainTextToken = Str::random(80);
        $token = ApiToken::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
            'user_id' => $user->id,
            'token' => hash('sha256', $this->plainTextToken),
            'is_active' => true,
        ]);

        $this->authRequest('postJson', '/api/v1/oauth/revoke')
            ->assertOk();

        $this->assertSoftDeleted('api_tokens', ['id' => $token->id]);
    }

    public function test_can_get_current_user(): void
    {
        $user = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $this->plainTextToken = Str::random(80);
        ApiToken::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
            'user_id' => $user->id,
            'token' => hash('sha256', $this->plainTextToken),
            'is_active' => true,
        ]);

        $this->authRequest('getJson', '/api/v1/oauth/me')
            ->assertOk()
            ->assertJsonStructure(['user' => ['id', 'email'], 'client', 'abilities']);
    }

    public function test_client_from_tenant_a_cannot_issue_token_for_user_from_tenant_b(): void
    {
        $schoolB = $this->createSchool('School B');
        $userB = User::factory()->create([
            'school_id' => $schoolB,
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/oauth/token', [
            'client_id' => $this->client->slug,
            'client_secret' => $this->client->secret,
            'email' => $userB->email,
            'password' => 'password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('credentials');
    }

    public function test_token_issued_for_tenant_a_cannot_be_used_when_tenancy_is_initialized_for_tenant_b(): void
    {
        $schoolB = $this->createSchool('School B');
        $userA = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $this->plainTextToken = Str::random(80);
        ApiToken::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
            'user_id' => $userA->id,
            'token' => hash('sha256', $this->plainTextToken),
            'is_active' => true,
        ]);

        Tenancy::initialize(School::query()->findOrFail($schoolB));

        $this->authRequest('getJson', '/api/v1/oauth/me')
            ->assertUnauthorized();

        if (tenancy()->initialized) {
            Tenancy::end();
        }
    }

    public function test_token_with_mismatched_school_binding_is_rejected(): void
    {
        $schoolB = $this->createSchool('School B');
        $userA = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $this->plainTextToken = Str::random(80);
        ApiToken::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $schoolB, // mismatched binding
            'user_id' => $userA->id,
            'token' => hash('sha256', $this->plainTextToken),
            'is_active' => true,
        ]);

        $this->authRequest('getJson', '/api/v1/oauth/me')
            ->assertUnauthorized();
    }

    public function test_user_without_valid_school_membership_is_rejected(): void
    {
        $user = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        DB::table('tenants')->where('id', '1')->update(['status' => 'inactive']);

        $response = $this->postJson('/api/v1/oauth/token', [
            'client_id' => $this->client->slug,
            'client_secret' => $this->client->secret,
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertUnprocessable();
    }

    public function test_unknown_or_unresolved_tenant_context_fails_closed(): void
    {
        $inactiveSchool = $this->createSchool('Inactive School', 'inactive');
        $client = ApiClient::factory()->create(['school_id' => $inactiveSchool]);
        $user = User::factory()->create([
            'school_id' => $inactiveSchool,
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/oauth/token', [
            'client_id' => $client->slug,
            'client_secret' => $client->secret,
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertUnprocessable();
    }

    public function test_invalid_or_revoked_client_cannot_issue_tokens(): void
    {
        $user = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $this->client->update(['is_active' => false]);

        $response = $this->postJson('/api/v1/oauth/token', [
            'client_id' => $this->client->slug,
            'client_secret' => $this->client->secret,
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertUnprocessable();
    }

    public function test_revoked_client_cannot_be_used_for_existing_token(): void
    {
        $user = User::factory()->create([
            'school_id' => '1',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $this->plainTextToken = Str::random(80);
        ApiToken::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
            'user_id' => $user->id,
            'token' => hash('sha256', $this->plainTextToken),
            'is_active' => true,
        ]);

        $this->client->update(['is_active' => false]);

        $this->authRequest('getJson', '/api/v1/oauth/me')
            ->assertUnauthorized();
    }

    private function createSchool(string $name, string $status = 'active'): string
    {
        $id = (string) Str::ulid();

        DB::table('tenants')->insert([
            'id' => $id,
            'uuid' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'database' => 'school_'.Str::lower(Str::random(8)),
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }
}
