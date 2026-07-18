<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiClientApiTest extends TestCase
{
    use RefreshDatabase;

    protected ApiClient $client;

    protected ApiToken $token;

    protected User $user;

    protected string $plainTextToken;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('tenants')->insert([
            'id' => '1', 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->user = User::factory()->create(['school_id' => '1']);
        $this->client = ApiClient::factory()->create(['school_id' => '1']);

        $this->plainTextToken = Str::random(80);
        $this->token = ApiToken::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
            'user_id' => $this->user->id,
            'token' => hash('sha256', $this->plainTextToken),
            'is_active' => true,
        ]);
    }

    private function authRequest(string $method, string $uri, array $data = [])
    {
        $headers = ['HTTP_AUTHORIZATION' => 'Bearer '.$this->plainTextToken];

        if (in_array($method, ['getJson'])) {
            return $this->$method($uri, $headers);
        }

        return $this->$method($uri, $data, $headers);
    }

    public function test_can_list_clients(): void
    {
        $this->authRequest('getJson', '/api/v1/clients')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_client(): void
    {
        $this->authRequest('postJson', '/api/v1/clients', [
            'name' => 'New API Client',
            'description' => 'A test client',
        ])->assertCreated();
    }

    public function test_can_show_client(): void
    {
        $this->authRequest('getJson', '/api/v1/clients/'.$this->client->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $this->client->id]);
    }

    public function test_can_update_client(): void
    {
        $this->authRequest('putJson', '/api/v1/clients/'.$this->client->id, [
            'name' => 'Updated Name',
        ])->assertOk();
    }

    public function test_can_delete_client(): void
    {
        $this->authRequest('deleteJson', '/api/v1/clients/'.$this->client->id)
            ->assertOk();
    }

    public function test_can_disable_client(): void
    {
        $this->authRequest('postJson', '/api/v1/clients/'.$this->client->id.'/disable')
            ->assertOk();
    }

    public function test_unauthenticated_returns_401(): void
    {
        $this->getJson('/api/v1/clients')
            ->assertUnauthorized();
    }
}
