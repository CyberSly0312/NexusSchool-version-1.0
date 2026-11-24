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

class ApiTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private ApiClient $clientA;
    private User $userA;
    private string $tokenA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchool('1', 'School A');
        $this->createSchool('2', 'School B');

        $this->clientA = ApiClient::factory()->create(['school_id' => '1']);
        $this->userA = User::factory()->create([
            'school_id' => '1',
            'status' => 'active',
        ]);

        $this->tokenA = Str::random(80);
        ApiToken::factory()->create([
            'api_client_id' => $this->clientA->id,
            'school_id' => '1',
            'user_id' => $this->userA->id,
            'token' => hash('sha256', $this->tokenA),
            'abilities' => ['*'],
            'is_active' => true,
        ]);
    }

    public function test_school_a_token_can_access_school_a_client_resources(): void
    {
        $this->getJson('/api/v1/clients/'.$this->clientA->id, $this->auth())
            ->assertOk()
            ->assertJsonPath('data.id', $this->clientA->id);
    }

    public function test_school_a_token_cannot_retrieve_school_b_resource_by_id(): void
    {
        $clientB = ApiClient::factory()->create(['school_id' => '2']);

        $this->getJson('/api/v1/clients/'.$clientB->id, $this->auth())
            ->assertUnprocessable();
    }

    public function test_school_a_token_cannot_list_school_b_records(): void
    {
        ApiClient::factory()->create(['school_id' => '2', 'name' => 'School B Client']);

        $response = $this->getJson('/api/v1/clients', $this->auth())
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($this->clientA->id, $ids);
        $this->assertCount(1, $ids);
    }

    public function test_school_a_token_cannot_modify_school_b_resource(): void
    {
        $clientB = ApiClient::factory()->create(['school_id' => '2', 'name' => 'Original B']);

        $this->putJson('/api/v1/clients/'.$clientB->id, ['name' => 'Hacked'], $this->auth())
            ->assertUnprocessable();

        $this->assertSame('Original B', $clientB->fresh()->name);
    }

    public function test_school_a_token_cannot_delete_school_b_resource(): void
    {
        $clientB = ApiClient::factory()->create(['school_id' => '2']);

        $this->deleteJson('/api/v1/clients/'.$clientB->id, [], $this->auth())
            ->assertUnprocessable();

        $this->assertDatabaseHas('api_clients', ['id' => $clientB->id, 'deleted_at' => null]);
    }

    public function test_request_payload_cannot_override_token_school_ownership_on_create(): void
    {
        $response = $this->postJson('/api/v1/clients', [
            'name' => 'Injected School',
            'school_id' => '2',
        ], $this->auth())->assertCreated();

        $createdId = $response->json('data.id');
        $this->assertDatabaseHas('api_clients', ['id' => $createdId, 'school_id' => '1']);
        $this->assertDatabaseMissing('api_clients', ['id' => $createdId, 'school_id' => '2']);
    }

    public function test_missing_or_invalid_token_context_fails_closed(): void
    {
        $this->getJson('/api/v1/clients')->assertUnauthorized();
        $this->getJson('/api/v1/clients', ['HTTP_AUTHORIZATION' => 'Bearer invalid-token'])->assertUnauthorized();
    }

    public function test_existing_legitimate_api_behavior_remains_functional(): void
    {
        $created = $this->postJson('/api/v1/clients', ['name' => 'Legit Client'], $this->auth())
            ->assertCreated();

        $id = $created->json('data.id');
        $this->getJson('/api/v1/clients/'.$id, $this->auth())->assertOk();
    }

    private function auth(): array
    {
        return ['HTTP_AUTHORIZATION' => 'Bearer '.$this->tokenA];
    }

    private function createSchool(string $id, string $name): void
    {
        DB::table('tenants')->insert([
            'id' => $id,
            'uuid' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'database' => 'school_'.Str::lower(Str::random(8)),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
