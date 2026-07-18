<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Ai;

use App\Domains\Ai\DTOs\AiKnowledgeSourceData;
use App\Domains\Ai\Services\KnowledgeService;
use App\Models\AiKnowledgeSource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KnowledgeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected KnowledgeService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KnowledgeService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_knowledge_source(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test Knowledge',
            'type' => 'markdown',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(AiKnowledgeSourceData::class, $result);
        $this->assertEquals('Test Knowledge', $result->name);
        $this->assertEquals('markdown', $result->type);
        $this->assertEquals('pending', $result->status);
    }

    public function test_can_update_knowledge_source(): void
    {
        $source = AiKnowledgeSource::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->update($source, ['description' => 'Updated']);
        $this->assertEquals('Updated', $result->description);
    }

    public function test_can_start_indexing(): void
    {
        $source = AiKnowledgeSource::factory()->forSchool($this->schoolId)->create(['status' => 'pending']);
        $result = $this->service->startIndexing($source);
        $this->assertEquals('indexing', $result->status);
    }

    public function test_can_complete_indexing(): void
    {
        $source = AiKnowledgeSource::factory()->forSchool($this->schoolId)->indexing()->create();
        $result = $this->service->completeIndexing($source, 10, 5000);
        $this->assertEquals('indexed', $result->status);
        $this->assertEquals(10, $result->chunkCount);
        $this->assertEquals(5000, $result->totalTokens);
    }

    public function test_can_mark_failed(): void
    {
        $source = AiKnowledgeSource::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->markFailed($source, 'Indexing error');
        $this->assertEquals('failed', $result->status);
    }

    public function test_can_delete_knowledge_source(): void
    {
        $source = AiKnowledgeSource::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($source);
        $this->assertSoftDeleted('ai_knowledge_sources', ['id' => $source->id]);
    }

    public function test_can_paginate_sources(): void
    {
        AiKnowledgeSource::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_duplicate_name_is_rejected(): void
    {
        AiKnowledgeSource::factory()->forSchool($this->schoolId)->create(['name' => 'Existing Source']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Existing Source',
            'type' => 'markdown',
        ]);
    }
}
