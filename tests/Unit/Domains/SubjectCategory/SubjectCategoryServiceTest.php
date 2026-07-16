<?php

namespace Tests\Unit\Domains\SubjectCategory;

use App\Domains\SubjectCategory\DTOs\SubjectCategoryData;
use App\Domains\SubjectCategory\Enums\SubjectCategoryStatus;
use App\Domains\SubjectCategory\Events\SubjectCategoryArchived;
use App\Domains\SubjectCategory\Events\SubjectCategoryCreated;
use App\Domains\SubjectCategory\Events\SubjectCategoryUpdated;
use App\Domains\SubjectCategory\Services\SubjectCategoryService;
use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SubjectCategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubjectCategoryService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SubjectCategoryService;

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

    public function test_can_create_subject_category(): void
    {
        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Languages',
            'code' => 'LANG',
            'description' => 'Language studies',
            'display_order' => 1,
            'color' => '#3B82F6',
            'icon' => 'book-open',
        ]);

        $subjectCategory = $this->service->create($data);

        $this->assertInstanceOf(SubjectCategory::class, $subjectCategory);
        $this->assertEquals('Languages', $subjectCategory->name);
        $this->assertEquals('LANG', $subjectCategory->code);
        $this->assertEquals(SubjectCategoryStatus::Active, $subjectCategory->status);
        $this->assertEquals('#3B82F6', $subjectCategory->color);
        $this->assertEquals('book-open', $subjectCategory->icon);
    }

    public function test_can_update_subject_category(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($subjectCategory, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_subject_category(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create();

        $deleted = $this->service->delete($subjectCategory);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('subject_categories', ['id' => $subjectCategory->id]);
    }

    public function test_can_archive_subject_category(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        $archived = $this->service->archive($subjectCategory);

        $this->assertEquals(SubjectCategoryStatus::Archived, $archived->status);
    }

    public function test_can_activate_subject_category(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        $activated = $this->service->activate($subjectCategory);

        $this->assertEquals(SubjectCategoryStatus::Active, $activated->status);
    }

    public function test_duplicate_name_same_school_is_rejected(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Languages',
            'code' => 'SCI',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_school_is_rejected(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Sciences',
            'code' => 'LANG',
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

        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $data = SubjectCategoryData::fromArray([
            'school_id' => $otherSchoolId,
            'name' => 'Languages',
            'code' => 'LANG',
        ]);

        $subjectCategory = $this->service->create($data);

        $this->assertNotNull($subjectCategory);
        $this->assertEquals('Languages', $subjectCategory->name);
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

        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $data = SubjectCategoryData::fromArray([
            'school_id' => $otherSchoolId,
            'name' => 'Languages',
            'code' => 'LANG',
        ]);

        $subjectCategory = $this->service->create($data);

        $this->assertNotNull($subjectCategory);
    }

    public function test_duplicate_display_order_is_rejected(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Sciences',
            'code' => 'SCI',
            'display_order' => 1,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_can_paginate_subject_categories(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            SubjectCategory::factory()
                ->forSchool($this->schoolId)
                ->withOrder($i)
                ->create();
        }

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            SubjectCategory::factory()
                ->forSchool($this->schoolId)
                ->active()
                ->withOrder($i)
                ->create();
        }
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->withOrder(4)
            ->create();

        $result = $this->service->paginate(['status' => SubjectCategoryStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Languages', 'code' => 'LANG']);
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(2)
            ->create(['name' => 'Sciences', 'code' => 'SCI']);

        $result = $this->service->paginate(['search' => 'Lang'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Languages', 'code' => 'LANG']);
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(2)
            ->create(['name' => 'Sciences', 'code' => 'SCI']);

        $result = $this->service->paginate(['search' => 'LANG'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([SubjectCategoryCreated::class]);

        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Languages',
            'code' => 'LANG',
        ]);

        $this->service->create($data);

        Event::assertDispatched(SubjectCategoryCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create();

        Event::fake([SubjectCategoryUpdated::class]);

        $this->service->update($subjectCategory, ['name' => 'Updated']);

        Event::assertDispatched(SubjectCategoryUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->active()
            ->create();

        Event::fake([SubjectCategoryArchived::class]);

        $this->service->archive($subjectCategory);

        Event::assertDispatched(SubjectCategoryArchived::class);
    }

    public function test_can_find_by_id(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create();

        $found = $this->service->findById($subjectCategory->id);

        $this->assertEquals($subjectCategory->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_school(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $cat2 = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Sciences', 'code' => 'SCI']);

        $this->expectException(ValidationException::class);

        $this->service->update($cat2, ['name' => 'Languages']);
    }

    public function test_can_update_same_subject_category_name(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $updated = $this->service->update($subjectCategory, ['description' => 'Updated']);

        $this->assertEquals('Languages', $updated->name);
        $this->assertEquals('Updated', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Languages',
            'code' => 'LANG',
        ]);

        $subjectCategory = $this->service->create($data);

        $this->assertNotEmpty($subjectCategory->uuid);
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

        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Languages', 'code' => 'LANG']);
        SubjectCategory::factory()
            ->forSchool($otherSchoolId)
            ->withOrder(2)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $result = $this->service->paginate(['school_id' => $this->schoolId], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_activate_dispatches_updated_event(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->archived()
            ->create();

        Event::fake([SubjectCategoryUpdated::class]);

        $this->service->activate($subjectCategory);

        Event::assertDispatched(SubjectCategoryUpdated::class);
    }

    public function test_update_validates_display_order_uniqueness(): void
    {
        SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $cat2 = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(2)
            ->create(['name' => 'Sciences', 'code' => 'SCI']);

        $this->expectException(ValidationException::class);

        $this->service->update($cat2, ['display_order' => 1]);
    }

    public function test_can_update_same_display_order(): void
    {
        $subjectCategory = SubjectCategory::factory()
            ->forSchool($this->schoolId)
            ->withOrder(1)
            ->create(['name' => 'Languages', 'code' => 'LANG']);

        $updated = $this->service->update($subjectCategory, ['description' => 'Updated']);

        $this->assertEquals(1, $updated->display_order);
        $this->assertEquals('Updated', $updated->description);
    }

    public function test_description_is_nullable(): void
    {
        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Languages',
            'code' => 'LANG',
        ]);

        $subjectCategory = $this->service->create($data);

        $this->assertNull($subjectCategory->description);
    }

    public function test_color_and_icon_are_nullable(): void
    {
        $data = SubjectCategoryData::fromArray([
            'school_id' => $this->schoolId,
            'name' => 'Languages',
            'code' => 'LANG',
        ]);

        $subjectCategory = $this->service->create($data);

        $this->assertNull($subjectCategory->color);
        $this->assertNull($subjectCategory->icon);
    }
}
