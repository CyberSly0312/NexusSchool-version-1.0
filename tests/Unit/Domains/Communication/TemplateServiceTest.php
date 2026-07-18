<?php

namespace Tests\Unit\Domains\Communication;

use App\Domains\Communication\Rules\NotificationTemplateRules;
use App\Domains\Communication\Services\TemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TemplateService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = 'test-school-'.uniqid();
        \DB::table('tenants')->insert([
            'id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->service = new TemplateService(
            new NotificationTemplateRules,
        );
    }

    public function test_can_create_template(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Fee Reminder',
            'slug' => 'fee-reminder',
            'category' => 'fee_reminder',
            'channel' => 'email',
            'subject' => 'Fee Reminder - {{ student_name }}',
            'body' => 'Dear {{ parent_name }}, please pay {{ amount }} by {{ date }}.',
            'variables' => ['student_name', 'parent_name', 'amount', 'date'],
        ];

        $template = $this->service->create($data);

        $this->assertNotNull($template);
        $this->assertDatabaseHas('notification_templates', ['slug' => 'fee-reminder', 'school_id' => $this->schoolId]);
    }

    public function test_can_update_template(): void
    {
        $template = $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Original Name',
            'slug' => 'original-name',
            'category' => 'general',
            'channel' => 'in_app',
            'body' => 'Original body.',
        ]);

        $updated = $this->service->update($template->id, [
            'name' => 'Updated Name',
            'body' => 'Updated body.',
        ]);

        $this->assertEquals('Updated Name', $updated->name);
    }

    public function test_can_activate_deactivate_template(): void
    {
        $template = $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Toggle Template',
            'slug' => 'toggle-template',
            'category' => 'general',
            'channel' => 'in_app',
            'body' => 'Toggle body.',
        ]);

        $deactivated = $this->service->deactivate($template->id);
        $this->assertFalse($deactivated->is_active);

        $activated = $this->service->activate($template->id);
        $this->assertTrue($activated->is_active);
    }

    public function test_can_render_template(): void
    {
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Render Test',
            'slug' => 'render-test',
            'category' => 'general',
            'channel' => 'in_app',
            'body' => 'Hello {{ name }}, your balance is {{ balance }}.',
        ]);

        $result = $this->service->render('render-test', [
            'name' => 'John',
            'balance' => '500',
        ], $this->schoolId);

        $this->assertNotNull($result);
        $this->assertStringContainsString('Hello John', $result['body']);
        $this->assertStringContainsString('500', $result['body']);
    }

    public function test_validates_template_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create([]);
    }
}
