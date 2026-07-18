<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Ai;

use App\Domains\Ai\DTOs\AiProviderData;
use App\Domains\Ai\Services\ProviderService;
use App\Models\AiProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProviderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProviderService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProviderService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_ai_provider(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test OpenAI',
            'driver' => 'openai',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(AiProviderData::class, $result);
        $this->assertEquals('Test OpenAI', $result->name);
        $this->assertEquals('openai', $result->driver);
        $this->assertEquals($this->schoolId, $result->schoolId);
    }

    public function test_can_update_ai_provider(): void
    {
        $provider = AiProvider::factory()->forSchool($this->schoolId)->openai()->create();
        $result = $this->service->update($provider, ['name' => 'Updated Provider']);
        $this->assertEquals('Updated Provider', $result->name);
    }

    public function test_can_enable_ai_provider(): void
    {
        $provider = AiProvider::factory()->forSchool($this->schoolId)->inactive()->create();
        $result = $this->service->enable($provider);
        $this->assertTrue($result->isActive);
    }

    public function test_can_disable_ai_provider(): void
    {
        $provider = AiProvider::factory()->forSchool($this->schoolId)->create(['is_active' => true]);
        $result = $this->service->disable($provider);
        $this->assertFalse($result->isActive);
    }

    public function test_can_delete_ai_provider(): void
    {
        $provider = AiProvider::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($provider);
        $this->assertSoftDeleted('ai_providers', ['id' => $provider->id]);
    }

    public function test_can_find_by_id(): void
    {
        $provider = AiProvider::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($provider->id);
        $this->assertEquals($provider->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->findById('non-existent-id');
    }

    public function test_can_paginate_providers(): void
    {
        AiProvider::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_duplicate_name_is_rejected(): void
    {
        AiProvider::factory()->forSchool($this->schoolId)->create(['name' => 'Existing Provider']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Existing Provider',
            'driver' => 'openai',
        ]);
    }

    public function test_can_find_active_for_school(): void
    {
        AiProvider::factory()->forSchool($this->schoolId)->openai()->create(['is_active' => true]);
        $found = $this->service->findActiveForSchool($this->schoolId, 'openai');
        $this->assertNotNull($found);
    }

    public function test_can_get_supported_models(): void
    {
        $models = $this->service->getSupportedModels('openai');
        $this->assertIsArray($models);
        $this->assertContains('gpt-4o', $models);
    }
}
