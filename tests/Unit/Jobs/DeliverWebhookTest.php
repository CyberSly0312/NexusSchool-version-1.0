<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Domains\Api\Services\WebhookService;
use App\Jobs\DeliverWebhook;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliverWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_deliver_webhook_success(): void
    {
        Http::fake(fn () => Http::response('{"ok": true}', 200));

        $webhook = Webhook::factory()->create([
            'school_id' => $this->schoolId,
            'url' => 'https://example.com/webhook',
            'is_active' => true,
        ]);

        $job = new DeliverWebhook($webhook->id, 'student.created', ['student_id' => '123']);
        $job->handle(app(WebhookService::class));

        $this->assertDatabaseHas('webhook_deliveries', [
            'webhook_id' => $webhook->id,
            'event' => 'student.created',
            'success' => true,
        ]);
    }

    public function test_deliver_webhook_failure(): void
    {
        Http::fake(fn () => Http::response('Server Error', 500));

        $webhook = Webhook::factory()->create([
            'school_id' => $this->schoolId,
            'url' => 'https://example.com/webhook',
            'is_active' => true,
        ]);

        $job = new DeliverWebhook($webhook->id, 'student.created', ['student_id' => '123']);
        $job->handle(app(WebhookService::class));

        $this->assertDatabaseHas('webhook_deliveries', [
            'webhook_id' => $webhook->id,
            'event' => 'student.created',
            'success' => false,
        ]);
        $webhook->refresh();
        $this->assertEquals(1, $webhook->failure_count);
    }

    public function test_skips_inactive_webhook(): void
    {
        Http::fake();

        $webhook = Webhook::factory()->create([
            'school_id' => $this->schoolId,
            'is_active' => false,
        ]);

        $job = new DeliverWebhook($webhook->id, 'student.created', ['student_id' => '123']);
        $job->handle(app(WebhookService::class));

        Http::assertNothingSent();
    }

    public function test_job_has_correct_try_count(): void
    {
        $job = new DeliverWebhook('test-id', 'student.created', []);

        $this->assertEquals(3, $job->tries);
    }

    public function test_job_has_correct_timeout(): void
    {
        $job = new DeliverWebhook('test-id', 'student.created', []);

        $this->assertEquals(30, $job->timeout);
    }
}
