<?php

namespace Tests\Unit\Domains\Communication;

use App\Domains\Communication\Rules\MessageRules;
use App\Domains\Communication\Services\MessagingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MessagingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MessagingService $service;

    protected string $schoolId;

    protected User $sender;

    protected User $recipient;

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

        $this->sender = User::factory()->create(['status' => 'active']);
        $this->recipient = User::factory()->create(['status' => 'active']);

        $this->service = new MessagingService(
            new MessageRules,
        );
    }

    public function test_can_create_message(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'sender_id' => (string) $this->sender->id,
            'subject' => 'Test Message',
            'body' => 'Hello, this is a test message.',
            'type' => 'direct',
            'recipient_ids' => [(string) $this->recipient->id],
        ];

        $message = $this->service->create($data);

        $this->assertNotNull($message);
        $this->assertDatabaseHas('messages', ['subject' => 'Test Message', 'school_id' => $this->schoolId]);
        $this->assertDatabaseHas('message_recipients', ['message_id' => $message->id]);
    }

    public function test_can_send_message(): void
    {
        $message = $this->service->create([
            'school_id' => $this->schoolId,
            'sender_id' => (string) $this->sender->id,
            'subject' => 'Send Test',
            'body' => 'Testing send.',
            'type' => 'direct',
            'recipient_ids' => [(string) $this->recipient->id],
        ]);

        $sent = $this->service->send($message->id);

        $this->assertEquals('sent', $sent->status->value);
        $this->assertNotNull($sent->sent_at);
    }

    public function test_can_mark_recipient_read(): void
    {
        $message = $this->service->create([
            'school_id' => $this->schoolId,
            'sender_id' => (string) $this->sender->id,
            'subject' => 'Read Test',
            'body' => 'Testing read.',
            'type' => 'direct',
            'recipient_ids' => [(string) $this->recipient->id],
        ]);

        $this->service->send($message->id);
        $this->service->markRecipientRead($message->id, (string) $this->recipient->id);

        $this->assertDatabaseHas('message_recipients', [
            'message_id' => $message->id,
            'recipient_id' => $this->recipient->id,
        ]);
    }

    public function test_can_archive_message(): void
    {
        $message = $this->service->create([
            'school_id' => $this->schoolId,
            'sender_id' => (string) $this->sender->id,
            'subject' => 'Archive Test',
            'body' => 'Testing archive.',
            'type' => 'direct',
            'recipient_ids' => [(string) $this->recipient->id],
        ]);

        $archived = $this->service->archive($message->id);

        $this->assertEquals('archived', $archived->status->value);
    }

    public function test_validates_message_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create([]);
    }
}
