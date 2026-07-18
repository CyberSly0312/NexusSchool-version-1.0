<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
}
