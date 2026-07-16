<?php

namespace Tests\Unit\Domains\Student;

use App\Domains\Student\DTOs\StudentData;
use App\Domains\Student\Enums\StudentStatus;
use App\Domains\Student\Events\StudentActivated;
use App\Domains\Student\Events\StudentArchived;
use App\Domains\Student\Events\StudentCreated;
use App\Domains\Student\Events\StudentUpdated;
use App\Domains\Student\Services\StudentService;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StudentService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StudentService;

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

    public function test_can_create_student(): void
    {
        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'date_of_birth' => '2010-01-15',
            'email' => 'john.doe@student.edu',
        ]);

        $student = $this->service->create($data);

        $this->assertInstanceOf(Student::class, $student);
        $this->assertEquals('STU-001', $student->student_number);
        $this->assertEquals('John', $student->first_name);
        $this->assertEquals('Doe', $student->last_name);
        $this->assertEquals('John Doe', $student->full_name);
        $this->assertEquals(StudentStatus::Active, $student->status);
    }

    public function test_can_update_student(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->create(['first_name' => 'John']);

        $updated = $this->service->update($student, ['first_name' => 'Johnny']);

        $this->assertEquals('Johnny', $updated->first_name);
    }

    public function test_can_delete_student(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->create();

        $deleted = $this->service->delete($student);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_can_archive_student(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($student);

        $this->assertEquals(StudentStatus::Archived, $archived->status);
    }

    public function test_can_activate_student(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $activated = $this->service->activate($student);

        $this->assertEquals(StudentStatus::Active, $activated->status);
    }

    public function test_duplicate_student_number_is_rejected(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->create(['student_number' => 'STU-001']);

        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'date_of_birth' => '2011-06-20',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_admission_number_is_rejected(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->create(['admission_number' => 'ADM-001']);

        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-002',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'date_of_birth' => '2011-06-20',
            'admission_number' => 'ADM-001',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_number_allowed_in_different_schools(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->create(['student_number' => 'STU-001']);

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

        $data = StudentData::fromArray([
            'school_id' => $otherSchoolId,
            'student_number' => 'STU-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'date_of_birth' => '2011-06-20',
        ]);

        $student = $this->service->create($data);

        $this->assertNotNull($student);
    }

    public function test_user_cannot_be_linked_to_multiple_students(): void
    {
        $user = User::factory()->create();

        Student::factory()
            ->forSchool($this->schoolId)
            ->linkedToUser($user->id)
            ->create();

        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-010',
            'first_name' => 'Another',
            'last_name' => 'User',
            'gender' => 'female',
            'date_of_birth' => '2012-03-10',
            'user_id' => $user->id,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'student_number' => 'STU-020',
            'first_name' => 'Test',
            'last_name' => 'User',
            'gender' => 'male',
            'date_of_birth' => '2010-05-15',
            'status' => 'invalid_status',
        ];

        $this->expectException(ValidationException::class);

        $this->service->create(StudentData::fromArray($data));
    }

    public function test_can_paginate_students(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->count(5)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->count(3)
            ->create();
        Student::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $result = $this->service->paginate(['status' => StudentStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->create(['first_name' => 'Kwame', 'last_name' => 'Asante']);
        Student::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();

        $result = $this->service->paginate(['search' => 'Kwame'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_student_number(): void
    {
        Student::factory()
            ->forSchool($this->schoolId)
            ->create(['student_number' => 'STU-SPECIAL']);
        Student::factory()
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

        Student::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();
        Student::factory()
            ->forSchool($otherSchoolId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([StudentCreated::class]);

        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-EVT-001',
            'first_name' => 'Event',
            'last_name' => 'Test',
            'gender' => 'male',
            'date_of_birth' => '2010-01-01',
        ]);

        $this->service->create($data);

        Event::assertDispatched(StudentCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->create();

        Event::fake([StudentUpdated::class]);

        $this->service->update($student, ['first_name' => 'Updated']);

        Event::assertDispatched(StudentUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([StudentArchived::class]);

        $this->service->archive($student);

        Event::assertDispatched(StudentArchived::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        Event::fake([StudentActivated::class]);

        $this->service->activate($student);

        Event::assertDispatched(StudentActivated::class);
    }

    public function test_can_find_by_id(): void
    {
        $student = Student::factory()
            ->forSchool($this->schoolId)
            ->create();

        $found = $this->service->findById($student->id);

        $this->assertEquals($student->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-UUID-001',
            'first_name' => 'UUID',
            'last_name' => 'Test',
            'gender' => 'male',
            'date_of_birth' => '2010-06-15',
        ]);

        $student = $this->service->create($data);

        $this->assertNotEmpty($student->uuid);
    }

    public function test_email_is_nullable(): void
    {
        $data = StudentData::fromArray([
            'school_id' => $this->schoolId,
            'student_number' => 'STU-NULL-001',
            'first_name' => 'Nullable',
            'last_name' => 'Field',
            'gender' => 'female',
            'date_of_birth' => '2011-08-20',
            'email' => null,
        ]);

        $student = $this->service->create($data);

        $this->assertNull($student->email);
    }
}
