<?php

namespace Tests\Unit\Domains\Subject;

use App\Domains\Subject\DTOs\SubjectData;
use App\Domains\Subject\Enums\SubjectStatus;
use App\Domains\Subject\Events\SubjectActivated;
use App\Domains\Subject\Events\SubjectArchived;
use App\Domains\Subject\Events\SubjectCreated;
use App\Domains\Subject\Events\SubjectUpdated;
use App\Domains\Subject\Services\SubjectService;
use App\Models\Department;
use App\Models\Program;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubjectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubjectService $service;

    protected string $schoolId;

    protected int $categoryId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SubjectService;

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

        $category = SubjectCategory::factory()->forSchool($schoolId)->create([
            'name' => 'General',
            'code' => 'GEN',
        ]);

        $this->categoryId = $category->id;
    }

    public function test_can_create_subject(): void
    {
        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'short_name' => 'Math',
            'description' => 'Algebra and geometry',
            'display_order' => 1,
            'color' => '#3B82F6',
            'is_elective' => false,
            'is_core_subject' => true,
            'weekly_hours' => 5,
        ]);

        $subject = $this->service->create($data);

        $this->assertInstanceOf(Subject::class, $subject);
        $this->assertEquals('Mathematics', $subject->name);
        $this->assertEquals('MATH101', $subject->code);
        $this->assertEquals('Math', $subject->short_name);
        $this->assertEquals(SubjectStatus::Active, $subject->status);
        $this->assertEquals(1, $subject->display_order);
        $this->assertEquals('#3B82F6', $subject->color);
        $this->assertFalse($subject->is_elective);
        $this->assertTrue($subject->is_core_subject);
        $this->assertEquals(5, $subject->weekly_hours);
    }

    public function test_can_update_subject(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($subject, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_subject(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create();

        $deleted = $this->service->delete($subject);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('subjects', ['id' => $subject->id]);
    }

    public function test_can_archive_subject(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($subject);

        $this->assertEquals(SubjectStatus::Archived, $archived->status);
    }

    public function test_can_activate_subject(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $activated = $this->service->activate($subject);

        $this->assertEquals(SubjectStatus::Active, $activated->status);
    }

    public function test_duplicate_name_same_school_is_rejected(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH102',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_school_is_rejected(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Physics',
            'code' => 'MATH101',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_name_allowed_in_different_schools(): void
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

        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $data = SubjectData::fromArray([
            'school_id' => $otherSchoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $subject = $this->service->create($data);

        $this->assertNotNull($subject);
        $this->assertEquals('Mathematics', $subject->name);
    }

    public function test_same_code_allowed_in_different_schools(): void
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

        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $data = SubjectData::fromArray([
            'school_id' => $otherSchoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $subject = $this->service->create($data);

        $this->assertNotNull($subject);
    }

    public function test_duplicate_display_order_is_rejected(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Physics',
            'code' => 'PHYS101',
            'display_order' => 1,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_core_and_elective_conflict_is_rejected(): void
    {
        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'is_elective' => true,
            'is_core_subject' => true,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_can_paginate_subjects(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->count(5)
            ->create();

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $result = $this->service->paginate(['status' => SubjectStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics']);
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Physics']);

        $result = $this->service->paginate(['search' => 'Math'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Physics', 'code' => 'PHYS101']);

        $result = $this->service->paginate(['search' => 'MATH'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_short_name(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'short_name' => 'Math']);
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Physics', 'short_name' => 'Phys']);

        $result = $this->service->paginate(['search' => 'Math'], 10);

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

        Subject::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($otherSchoolId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_subject_category(): void
    {
        $category2 = SubjectCategory::factory()->forSchool($this->schoolId)->create();

        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($category2->id)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['subject_category_id' => $this->categoryId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_department(): void
    {
        $department1 = Department::factory()->forSchool($this->schoolId)->create();
        $department2 = Department::factory()->forSchool($this->schoolId)->create();

        Subject::factory()
            ->forSchool($this->schoolId)
            ->forDepartment($department1->id)
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forDepartment($department2->id)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['department_id' => $department1->id], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_program(): void
    {
        $program1 = Program::factory()->forSchool($this->schoolId)->create();
        $program2 = Program::factory()->forSchool($this->schoolId)->create();

        Subject::factory()
            ->forSchool($this->schoolId)
            ->forProgram($program1->id)
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($this->schoolId)
            ->forProgram($program2->id)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['program_id' => $program1->id], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_is_core_subject(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->core()
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($this->schoolId)
            ->elective()
            ->count(2)
            ->create();

        $result = $this->service->paginate(['is_core_subject' => true], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_by_is_elective(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->elective()
            ->count(2)
            ->create();
        Subject::factory()
            ->forSchool($this->schoolId)
            ->core()
            ->count(3)
            ->create();

        $result = $this->service->paginate(['is_elective' => true], 10);

        $this->assertEquals(2, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([SubjectCreated::class]);

        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $this->service->create($data);

        Event::assertDispatched(SubjectCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create();

        Event::fake([SubjectUpdated::class]);

        $this->service->update($subject, ['name' => 'Updated Name']);

        Event::assertDispatched(SubjectUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([SubjectArchived::class]);

        $this->service->archive($subject);

        Event::assertDispatched(SubjectArchived::class);
    }

    public function test_events_dispatched_on_activate(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        Event::fake([SubjectActivated::class]);

        $this->service->activate($subject);

        Event::assertDispatched(SubjectActivated::class);
    }

    public function test_can_find_by_id(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create();

        $found = $this->service->findById($subject->id);

        $this->assertEquals($subject->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_school(): void
    {
        $sub1 = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $sub2 = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Physics', 'code' => 'PHYS101']);

        $this->expectException(ValidationException::class);

        $this->service->update($sub2, ['name' => 'Mathematics']);
    }

    public function test_can_update_same_subject_name(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->forCategory($this->categoryId)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $updated = $this->service->update($subject, ['description' => 'Updated description']);

        $this->assertEquals('Mathematics', $updated->name);
        $this->assertEquals('Updated description', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $subject = $this->service->create($data);

        $this->assertNotEmpty($subject->uuid);
    }

    public function test_can_filter_by_school_id(): void
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

        Subject::factory()
            ->forSchool($this->schoolId)
            ->count(3)
            ->create();
        Subject::factory()
            ->forSchool($otherSchoolId)
            ->count(2)
            ->create();

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_update_validates_display_order_uniqueness(): void
    {
        Subject::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $sub2 = Subject::factory()
            ->forSchool($this->schoolId)
            ->withOrder(2)
            ->create(['name' => 'Physics', 'code' => 'PHYS101']);

        $this->expectException(ValidationException::class);

        $this->service->update($sub2, ['display_order' => 1]);
    }

    public function test_can_update_same_display_order(): void
    {
        $subject = Subject::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Mathematics', 'code' => 'MATH101']);

        $updated = $this->service->update($subject, ['description' => 'Updated']);

        $this->assertEquals(1, $updated->display_order);
        $this->assertEquals('Updated', $updated->description);
    }

    public function test_description_is_nullable(): void
    {
        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $subject = $this->service->create($data);

        $this->assertNull($subject->description);
    }

    public function test_color_and_short_name_are_nullable(): void
    {
        $data = SubjectData::fromArray([
            'school_id' => $this->schoolId,
            'subject_category_id' => $this->categoryId,
            'name' => 'Mathematics',
            'code' => 'MATH101',
        ]);

        $subject = $this->service->create($data);

        $this->assertNull($subject->color);
        $this->assertNull($subject->short_name);
    }
}
