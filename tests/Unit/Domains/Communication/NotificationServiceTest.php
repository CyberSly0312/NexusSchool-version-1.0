<?php

namespace Tests\Unit\Domains\Communication;

use App\Domains\Communication\Rules\NotificationRules;
use App\Domains\Communication\Services\NotificationService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $service;

    protected string $schoolId;

    protected User $user;

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

        $this->user = User::factory()->create(['status' => 'active']);

        $this->service = new NotificationService(
            new NotificationRules,
        );
    }

    public function test_can_create_notification(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'type' => 'system',
            'title' => 'Test Notification',
            'body' => 'This is a test notification.',
            'channel' => 'in_app',
            'recipient_ids' => [(string) $this->user->id],
        ];

        $notification = $this->service->create($data);

        $this->assertNotNull($notification);
        $this->assertDatabaseHas('notifications', ['title' => 'Test Notification', 'school_id' => $this->schoolId]);
        $this->assertDatabaseHas('notification_deliveries', ['notification_id' => $notification->id]);
    }

    public function test_can_queue_notification(): void
    {
        $notification = $this->service->create([
            'school_id' => $this->schoolId,
            'type' => 'system',
            'title' => 'Queue Test',
            'body' => 'Testing queue.',
            'recipient_ids' => [(string) $this->user->id],
        ]);

        $queued = $this->service->queue($notification->id);

        $this->assertEquals('queued', $queued->status->value);
    }

    public function test_can_send_notification(): void
    {
        $notification = $this->service->create([
            'school_id' => $this->schoolId,
            'type' => 'system',
            'title' => 'Send Test',
            'body' => 'Testing send.',
            'recipient_ids' => [(string) $this->user->id],
        ]);

        $this->service->queue($notification->id);
        $sent = $this->service->send($notification->id);

        $this->assertEquals('sent', $sent->status->value);
        $this->assertNotNull($sent->sent_at);
    }

    public function test_can_cancel_notification(): void
    {
        $notification = $this->service->create([
            'school_id' => $this->schoolId,
            'type' => 'system',
            'title' => 'Cancel Test',
            'body' => 'Testing cancel.',
            'recipient_ids' => [(string) $this->user->id],
        ]);

        $cancelled = $this->service->cancel($notification->id);

        $this->assertEquals('cancelled', $cancelled->status->value);
    }

    public function test_validates_notification_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create([]);
    }

    public function test_can_mark_read(): void
    {
        $notification = $this->service->create([
            'school_id' => $this->schoolId,
            'type' => 'system',
            'title' => 'Read Test',
            'body' => 'Testing read.',
            'recipient_ids' => [(string) $this->user->id],
        ]);

        $this->service->markRead($notification->id);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
        ]);
    }
}
