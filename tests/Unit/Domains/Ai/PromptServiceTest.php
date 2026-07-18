<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Ai;

use App\Domains\Ai\DTOs\AiPromptTemplateData;
use App\Domains\Ai\Services\PromptService;
use App\Models\AiPromptTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PromptServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PromptService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PromptService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_prompt_template(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test Template',
            'category' => 'student_assistant',
            'system_prompt' => 'You are helpful.',
            'user_prompt_template' => 'Help with {{topic}}',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(AiPromptTemplateData::class, $result);
        $this->assertEquals('Test Template', $result->name);
        $this->assertEquals('student_assistant', $result->category);
        $this->assertEquals(1, $result->version);
    }

    public function test_can_update_prompt_template(): void
    {
        $template = AiPromptTemplate::factory()->forSchool($this->schoolId)->create();
        $result = $this->service->update($template, ['description' => 'Updated description']);
        $this->assertEquals('Updated description', $result->description);
    }

    public function test_version_increments_on_prompt_change(): void
    {
        $template = AiPromptTemplate::factory()->forSchool($this->schoolId)->create(['version' => 1]);
        $result = $this->service->update($template, ['system_prompt' => 'New system prompt']);
        $this->assertEquals(2, $result->version);
    }

    public function test_can_delete_prompt_template(): void
    {
        $template = AiPromptTemplate::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($template);
        $this->assertSoftDeleted('ai_prompt_templates', ['id' => $template->id]);
    }

    public function test_can_find_by_id(): void
    {
        $template = AiPromptTemplate::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($template->id);
        $this->assertEquals($template->id, $found->id);
    }

    public function test_can_paginate_templates(): void
    {
        AiPromptTemplate::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_duplicate_name_is_rejected(): void
    {
        AiPromptTemplate::factory()->forSchool($this->schoolId)->create(['name' => 'Existing Template']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Existing Template',
            'category' => 'student_assistant',
            'system_prompt' => 'test',
            'user_prompt_template' => 'test',
        ]);
    }

    public function test_can_render_template(): void
    {
        $template = AiPromptTemplate::factory()->forSchool($this->schoolId)->create([
            'user_prompt_template' => 'Help with {{topic}} for {{grade}}',
        ]);
        $rendered = $this->service->render($template->id, ['topic' => 'Math', 'grade' => '10th']);
        $this->assertEquals('Help with Math for 10th', $rendered);
    }
}
