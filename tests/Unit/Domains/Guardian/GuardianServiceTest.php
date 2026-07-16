<?php

namespace Tests\Unit\Domains\Guardian;

use App\Domains\Guardian\DTOs\GuardianData;
use App\Domains\Guardian\Enums\GuardianStatus;
use App\Domains\Guardian\Events\GuardianActivated;
use App\Domains\Guardian\Events\GuardianArchived;
use App\Domains\Guardian\Events\GuardianCreated;
use App\Domains\Guardian\Events\GuardianUpdated;
use App\Domains\Guardian\Services\GuardianService;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GuardianServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GuardianService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new GuardianService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test_school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->schoolId = $schoolId;
    }

    public function test_can_create_guardian(): void
    {
        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'relationship_type' => 'father',
            'email' => 'john.doe@example.com',
            'phone_number' => '+233 24 100 0001',
        ]);

        $guardian = $this->service->create($data);

        $this->assertInstanceOf(Guardian::class, $guardian);
        $this->assertEquals('GUA-001', $guardian->guardian_number);
        $this->assertEquals('John', $guardian->first_name);
        $this->assertEquals('Doe', $guardian->last_name);
        $this->assertEquals('John Doe', $guardian->full_name);
        $this->assertEquals(GuardianStatus::Active, $guardian->status);
    }

    public function test_can_update_guardian(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['first_name' => 'John']);

        $updated = $this->service->update($guardian, ['first_name' => 'Johnny']);

        $this->assertEquals('Johnny', $updated->first_name);
    }

    public function test_can_delete_guardian(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->create();

        $deleted = $this->service->delete($guardian);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('guardians', ['id' => $guardian->id]);
    }

    public function test_can_archive_guardian(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($guardian);

        $this->assertEquals(GuardianStatus::Archived, $archived->status);
    }

    public function test_can_activate_guardian(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $activated = $this->service->activate($guardian);

        $this->assertEquals(GuardianStatus::Active, $activated->status);
    }

    public function test_duplicate_guardian_number_is_rejected(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['guardian_number' => 'GUA-001']);

        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'relationship_type' => 'mother',
            'phone_number' => '+233 24 100 0002',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_phone_number_is_rejected(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['phone_number' => '+233 24 100 0001']);

        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-002',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'relationship_type' => 'mother',
            'phone_number' => '+233 24 100 0001',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['email' => 'jane@example.com']);

        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-003',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'gender' => 'female',
            'relationship_type' => 'mother',
            'email' => 'jane@example.com',
            'phone_number' => '+233 24 100 0003',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_invalid_relationship_type_is_rejected(): void
    {
        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-004',
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 'male',
            'relationship_type' => 'invalid_type',
            'phone_number' => '+233 24 100 0004',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_invalid_preferred_contact_method_is_rejected(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-005',
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 'male',
            'relationship_type' => 'father',
            'phone_number' => '+233 24 100 0005',
            'preferred_contact_method' => 'invalid_method',
        ];

        $this->expectException(ValidationException::class);

        $this->service->create(GuardianData::fromArray($data));
    }

    public function test_same_number_allowed_in_different_schools(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['guardian_number' => 'GUA-001']);

        $otherSchoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $otherSchoolId,
            'uuid' => Str::ulid(),
            'name' => 'Other School',
            'slug' => 'other-school-'.uniqid(),
            'database' => 'school_other',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = GuardianData::fromArray([
            'school_id' => $otherSchoolId,
            'guardian_number' => 'GUA-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'relationship_type' => 'mother',
            'phone_number' => '+233 24 100 0002',
        ]);

        $guardian = $this->service->create($data);

        $this->assertNotNull($guardian);
    }

    public function test_user_cannot_be_linked_to_multiple_guardians(): void
    {
        $user = User::factory()->create();

        Guardian::factory()
            ->forSchool($this->schoolId)
            ->linkedToUser($user->id)
            ->create();

        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-010',
            'first_name' => 'Another',
            'last_name' => 'User',
            'gender' => 'female',
            'relationship_type' => 'mother',
            'phone_number' => '+233 24 100 0010',
            'user_id' => $user->id,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_can_paginate_guardians(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->count(5)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->count(3)
            ->create();
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $result = $this->service->paginate(['status' => GuardianStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['first_name' => 'Kwame', 'last_name' => 'Asante']);
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();

        $result = $this->service->paginate(['search' => 'Kwame'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_guardian_number(): void
    {
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->create(['guardian_number' => 'GUA-SPECIAL']);
        Guardian::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();

        $result = $this->service->paginate(['search' => 'SPECIAL'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_school(): void
    {
        $otherSchoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $otherSchoolId,
            'uuid' => Str::ulid(),
            'name' => 'Other School',
            'slug' => 'other-school-'.uniqid(),
            'database' => 'school_other',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Guardian::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();
        Guardian::factory()
            ->forSchool($otherSchoolId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([GuardianCreated::class]);

        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-EVT-001',
            'first_name' => 'Event',
            'last_name' => 'Test',
            'gender' => 'male',
            'relationship_type' => 'father',
            'phone_number' => '+233 24 100 0101',
        ]);

        $this->service->create($data);

        Event::assertDispatched(GuardianCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->create();

        Event::fake([GuardianUpdated::class]);

        $this->service->update($guardian, ['first_name' => 'Updated']);

        Event::assertDispatched(GuardianUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([GuardianArchived::class]);

        $this->service->archive($guardian);

        Event::assertDispatched(GuardianArchived::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        Event::fake([GuardianActivated::class]);

        $this->service->activate($guardian);

        Event::assertDispatched(GuardianActivated::class);
    }

    public function test_can_find_by_id(): void
    {
        $guardian = Guardian::factory()
            ->forSchool($this->schoolId)
            ->create();

        $found = $this->service->findById($guardian->id);

        $this->assertEquals($guardian->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-UUID-001',
            'first_name' => 'UUID',
            'last_name' => 'Test',
            'gender' => 'male',
            'relationship_type' => 'father',
            'phone_number' => '+233 24 100 0201',
        ]);

        $guardian = $this->service->create($data);

        $this->assertNotEmpty($guardian->uuid);
    }

    public function test_email_is_nullable(): void
    {
        $data = GuardianData::fromArray([
            'school_id' => $this->schoolId,
            'guardian_number' => 'GUA-NULL-001',
            'first_name' => 'Nullable',
            'last_name' => 'Field',
            'gender' => 'female',
            'relationship_type' => 'mother',
            'phone_number' => '+233 24 100 0301',
            'email' => null,
        ]);

        $guardian = $this->service->create($data);

        $this->assertNull($guardian->email);
    }
}
