<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Ai;

use App\Domains\Ai\DTOs\AiConversationData;
use App\Domains\Ai\Services\ConversationService;
use App\Models\AiConversation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ConversationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ConversationService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConversationService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_conversation(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'title' => 'Test Conversation',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(AiConversationData::class, $result);
        $this->assertEquals('Test Conversation', $result->title);
        $this->assertEquals('active', $result->status);
    }

    public function test_can_update_conversation(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->update($conversation, ['title' => 'Updated Title']);
        $this->assertEquals('Updated Title', $result->title);
    }

    public function test_can_archive_conversation(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create(['status' => 'active']);
        $result = $this->service->archive($conversation);
        $this->assertEquals('archived', $result->status);
    }

    public function test_can_close_conversation(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create(['status' => 'active']);
        $result = $this->service->close($conversation);
        $this->assertEquals('closed', $result->status);
    }

    public function test_can_summarize_conversation(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->summarize($conversation, 'This is a summary.');
        $this->assertEquals('This is a summary.', $result->summary);
        $this->assertEquals('summarized', $result->status);
    }

    public function test_can_add_message(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->addMessage($conversation, [
            'role' => 'user',
            'content' => 'Hello AI!',
        ]);
        $this->assertEquals('user', $result->role);
        $this->assertEquals('Hello AI!', $result->content);
    }

    public function test_can_get_messages(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create();
        $this->service->addMessage($conversation, ['role' => 'user', 'content' => 'Hello']);
        $this->service->addMessage($conversation, ['role' => 'assistant', 'content' => 'Hi there!']);

        $messages = $this->service->getMessages($conversation->id);
        $this->assertCount(2, $messages);
    }

    public function test_can_find_by_id(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($conversation->id);
        $this->assertEquals($conversation->id, $found->id);
    }

    public function test_can_paginate_conversations(): void
    {
        AiConversation::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_can_delete_conversation(): void
    {
        $conversation = AiConversation::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($conversation);
        $this->assertSoftDeleted('ai_conversations', ['id' => $conversation->id]);
    }
}
