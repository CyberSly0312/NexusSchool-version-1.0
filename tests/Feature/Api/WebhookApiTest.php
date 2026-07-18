<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\ApiClient;
use App\Models\ApiToken;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebhookApiTest extends TestCase
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

    public function test_can_list_webhooks(): void
    {
        $this->authRequest('getJson', '/api/v1/clients/'.$this->client->id.'/webhooks')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_webhook(): void
    {
        $this->authRequest('postJson', '/api/v1/clients/'.$this->client->id.'/webhooks', [
            'url' => 'https://example.com/webhook',
            'events' => ['student.created'],
        ])->assertCreated();
    }

    public function test_can_show_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
        ]);

        $this->authRequest('getJson', '/api/v1/clients/'.$this->client->id.'/webhooks/'.$webhook->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $webhook->id]);
    }

    public function test_can_update_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
        ]);

        $this->authRequest('putJson', '/api/v1/clients/'.$this->client->id.'/webhooks/'.$webhook->id, [
            'url' => 'https://updated.example.com/webhook',
        ])->assertOk();
    }

    public function test_can_delete_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
        ]);

        $this->authRequest('deleteJson', '/api/v1/clients/'.$this->client->id.'/webhooks/'.$webhook->id)
            ->assertOk();
    }

    public function test_can_disable_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => '1',
        ]);

        $this->authRequest('postJson', '/api/v1/clients/'.$this->client->id.'/webhooks/'.$webhook->id.'/disable')
            ->assertOk();
    }

    public function test_unauthenticated_returns_401(): void
    {
        $this->getJson('/api/v1/clients/'.$this->client->id.'/webhooks')
            ->assertUnauthorized();
    }
}
