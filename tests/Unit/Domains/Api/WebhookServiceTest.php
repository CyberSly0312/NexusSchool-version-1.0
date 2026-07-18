<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api;

use App\Domains\Api\DTOs\WebhookData;
use App\Domains\Api\DTOs\WebhookDeliveryData;
use App\Domains\Api\Events\WebhookRegistered;
use App\Domains\Api\Services\WebhookService;
use App\Models\ApiClient;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebhookServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WebhookService $service;

    protected string $schoolId;

    protected ApiClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WebhookService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
        $this->client = ApiClient::factory()->forSchool($this->schoolId)->create();
    }

    public function test_can_create_webhook(): void
    {
        $result = $this->service->create($this->client, [
            'url' => 'https://example.com/webhook',
            'events' => ['student.created'],
        ]);

        $this->assertInstanceOf(WebhookData::class, $result);
        $this->assertEquals('https://example.com/webhook', $result->url);
        $this->assertEquals(['student.created'], $result->events);
    }

    public function test_can_update_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
            'url' => 'https://old.example.com/webhook',
        ]);

        $result = $this->service->update($webhook, [
            'url' => 'https://new.example.com/webhook',
        ]);

        $this->assertEquals('https://new.example.com/webhook', $result->url);
    }

    public function test_can_delete_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
        ]);

        $this->service->delete($webhook);

        $this->assertSoftDeleted('webhooks', ['id' => $webhook->id]);
    }

    public function test_can_disable_webhook(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
            'is_active' => true,
        ]);

        $result = $this->service->disable($webhook);

        $this->assertFalse($result->isActive);
    }

    public function test_can_enable_webhook(): void
    {
        $webhook = Webhook::factory()->inactive()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
        ]);

        $result = $this->service->enable($webhook);

        $this->assertTrue($result->isActive);
    }

    public function test_can_record_successful_delivery(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
        ]);

        $result = $this->service->recordDelivery(
            $webhook,
            'student.created',
            ['student_id' => '123'],
            200,
            '{"ok": true}',
            true,
        );

        $this->assertInstanceOf(WebhookDeliveryData::class, $result);
        $this->assertTrue($result->success);
    }

    public function test_can_record_failed_delivery(): void
    {
        $webhook = Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
            'failure_count' => 0,
        ]);

        $result = $this->service->recordDelivery(
            $webhook,
            'student.created',
            ['student_id' => '123'],
            500,
            'Server Error',
            false,
        );

        $this->assertFalse($result->success);
        $webhook->refresh();
        $this->assertEquals(1, $webhook->failure_count);
    }

    public function test_can_get_active_for_event(): void
    {
        Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
            'events' => ['student.created'],
            'is_active' => true,
        ]);

        Webhook::factory()->create([
            'api_client_id' => $this->client->id,
            'school_id' => $this->schoolId,
            'events' => ['payment.received'],
            'is_active' => true,
        ]);

        $webhooks = $this->service->getActiveForEvent($this->schoolId, 'student.created');

        $this->assertCount(1, $webhooks);
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([WebhookRegistered::class]);

        $this->service->create($this->client, [
            'url' => 'https://example.com/webhook',
            'events' => ['student.created'],
        ]);

        Event::assertDispatched(WebhookRegistered::class);
    }
}
