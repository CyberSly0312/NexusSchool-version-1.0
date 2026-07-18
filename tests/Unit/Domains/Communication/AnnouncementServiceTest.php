<?php

namespace Tests\Unit\Domains\Communication;

use App\Domains\Communication\Rules\AnnouncementRules;
use App\Domains\Communication\Services\AnnouncementService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AnnouncementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnnouncementService $service;

    protected string $schoolId;

    protected User $author;

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

        $this->author = User::factory()->create(['status' => 'active']);

        $this->service = new AnnouncementService(
            new AnnouncementRules,
        );
    }

    public function test_can_create_announcement(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'author_id' => (string) $this->author->id,
            'title' => 'Test Announcement',
            'body' => 'This is a test announcement for the school.',
            'scope' => 'school_wide',
            'priority' => 'normal',
        ];

        $announcement = $this->service->create($data);

        $this->assertNotNull($announcement);
        $this->assertDatabaseHas('announcements', ['title' => 'Test Announcement', 'school_id' => $this->schoolId]);
    }

    public function test_can_publish_announcement(): void
    {
        $announcement = $this->service->create([
            'school_id' => $this->schoolId,
            'author_id' => (string) $this->author->id,
            'title' => 'Publish Test',
            'body' => 'Testing publish.',
            'scope' => 'school_wide',
        ]);

        $published = $this->service->publish($announcement->id);

        $this->assertEquals('published', $published->status);
        $this->assertNotNull($published->published_at);
    }

    public function test_cannot_edit_published_announcement(): void
    {
        $announcement = $this->service->create([
            'school_id' => $this->schoolId,
            'author_id' => (string) $this->author->id,
            'title' => 'Cannot Edit',
            'body' => 'Testing.',
            'scope' => 'school_wide',
        ]);

        $this->service->publish($announcement->id);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Published announcements cannot be edited.');

        $this->service->update($announcement->id, ['title' => 'Updated Title']);
    }

    public function test_can_archive_announcement(): void
    {
        $announcement = $this->service->create([
            'school_id' => $this->schoolId,
            'author_id' => (string) $this->author->id,
            'title' => 'Archive Test',
            'body' => 'Testing archive.',
            'scope' => 'school_wide',
        ]);

        $archived = $this->service->archive($announcement->id);

        $this->assertEquals('archived', $archived->status);
    }

    public function test_validates_announcement_data(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create([]);
    }
}
