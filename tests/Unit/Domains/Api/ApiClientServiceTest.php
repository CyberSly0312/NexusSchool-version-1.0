<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api;

use App\Domains\Api\DTOs\ApiClientData;
use App\Domains\Api\Events\ApiClientDisabled;
use App\Domains\Api\Services\ApiClientService;
use App\Models\ApiClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ApiClientServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ApiClientService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApiClientService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_api_client(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test Client',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(ApiClientData::class, $result);
        $this->assertEquals('Test Client', $result->name);
        $this->assertEquals('test-client', $result->slug);
        $this->assertEquals($this->schoolId, $result->schoolId);
    }

    public function test_can_update_api_client(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create(['name' => 'Old Name']);
        $result = $this->service->update($client, ['name' => 'New Name']);
        $this->assertEquals('New Name', $result->name);
    }

    public function test_can_disable_api_client(): void
    {
        Event::fake([ApiClientDisabled::class]);
        $client = ApiClient::factory()->forSchool($this->schoolId)->create(['is_active' => true]);
        $result = $this->service->disable($client);
        $this->assertFalse($result->isActive);
        Event::assertDispatched(ApiClientDisabled::class);
    }

    public function test_can_enable_api_client(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->inactive()->create();
        $result = $this->service->enable($client);
        $this->assertTrue($result->isActive);
    }

    public function test_can_delete_api_client(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($client);
        $this->assertSoftDeleted('api_clients', ['id' => $client->id]);
    }

    public function test_can_find_by_id(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($client->id);
        $this->assertEquals($client->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->findById('non-existent-id');
    }

    public function test_can_paginate_clients(): void
    {
        ApiClient::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_can_regenerate_secret(): void
    {
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $originalSecret = $client->secret;
        $result = $this->service->regenerateSecret($client);
        $this->assertNotEquals($originalSecret, $result->secret);
    }

    public function test_duplicate_name_is_rejected(): void
    {
        ApiClient::factory()->forSchool($this->schoolId)->create(['name' => 'Existing Client']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Existing Client',
        ]);
    }

    public function test_events_dispatched_on_disable(): void
    {
        Event::fake([ApiClientDisabled::class]);
        $client = ApiClient::factory()->forSchool($this->schoolId)->create();
        $this->service->disable($client);
        Event::assertDispatched(ApiClientDisabled::class);
    }
}
