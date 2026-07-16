<?php

namespace Tests\Unit\Domains\Section;

use App\Domains\Section\DTOs\SectionData;
use App\Domains\Section\Enums\SectionStatus;
use App\Domains\Section\Events\SectionArchived;
use App\Domains\Section\Events\SectionCreated;
use App\Domains\Section\Events\SectionUpdated;
use App\Domains\Section\Services\SectionService;
use App\Models\GradeLevel;
use App\Models\Program;
use App\Models\Section;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SectionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SectionService $service;

    protected string $schoolId;

    protected GradeLevel $gradeLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SectionService;

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

        $program = Program::factory()
            ->forSchool($schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);

        $this->gradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);
    }

    public function test_can_create_section(): void
    {
        $data = SectionData::fromArray([
            'grade_level_id' => $this->gradeLevel->id,
            'name' => 'Section A',
            'code' => 'A',
            'description' => 'First section',
            'order' => 1,
        ]);

        $section = $this->service->create($data);

        $this->assertInstanceOf(Section::class, $section);
        $this->assertEquals('Section A', $section->name);
        $this->assertEquals('A', $section->code);
        $this->assertEquals(1, $section->order);
        $this->assertEquals(SectionStatus::Active, $section->status);
    }

    public function test_can_update_section(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($section, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_section(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create();

        $deleted = $this->service->delete($section);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('sections', ['id' => $section->id]);
    }

    public function test_can_archive_section(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->active()
            ->create();

        $archived = $this->service->archive($section);

        $this->assertEquals(SectionStatus::Archived, $archived->status);
    }

    public function test_can_restore_section(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->archived()
            ->create();

        $restored = $this->service->restore($section);

        $this->assertEquals(SectionStatus::Active, $restored->status);
    }

    public function test_duplicate_name_same_grade_level_is_rejected(): void
    {
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A']);

        $data = SectionData::fromArray([
            'grade_level_id' => $this->gradeLevel->id,
            'name' => 'Section A',
            'code' => 'B',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_grade_level_is_rejected(): void
    {
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A']);

        $data = SectionData::fromArray([
            'grade_level_id' => $this->gradeLevel->id,
            'name' => 'Section B',
            'code' => 'A',
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_name_allowed_in_different_grade_levels(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High', 'code' => 'SHS']);

        $otherGradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->withOrder(1)
            ->create(['name' => 'Seconde', 'code' => 'SEC']);

        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A']);

        $data = SectionData::fromArray([
            'grade_level_id' => $otherGradeLevel->id,
            'name' => 'Section A',
            'code' => 'A',
        ]);

        $section = $this->service->create($data);

        $this->assertNotNull($section);
        $this->assertEquals('Section A', $section->name);
    }

    public function test_can_paginate_sections(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Section::factory()
                ->forGradeLevel($this->gradeLevel->id)
                ->create(['order' => $i]);
        }

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            Section::factory()
                ->forGradeLevel($this->gradeLevel->id)
                ->active()
                ->create(['order' => $i]);
        }
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->archived()
            ->create(['order' => 4]);

        $result = $this->service->paginate(['status' => SectionStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A', 'order' => 1]);
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section B', 'code' => 'B', 'order' => 2]);

        $result = $this->service->paginate(['search' => 'Section A'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A', 'order' => 1]);
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section B', 'code' => 'B', 'order' => 2]);

        $result = $this->service->paginate(['search' => 'A'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_grade_level(): void
    {
        $program = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High', 'code' => 'SHS']);

        $otherGradeLevel = GradeLevel::factory()
            ->forProgram($program->id)
            ->withOrder(1)
            ->create(['name' => 'Seconde', 'code' => 'SEC']);

        for ($i = 1; $i <= 3; $i++) {
            Section::factory()
                ->forGradeLevel($this->gradeLevel->id)
                ->create(['order' => $i]);
        }
        for ($i = 1; $i <= 2; $i++) {
            Section::factory()
                ->forGradeLevel($otherGradeLevel->id)
                ->create(['order' => $i]);
        }

        $result = $this->service->paginate(['grade_level_id' => $this->gradeLevel->id], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([SectionCreated::class]);

        $data = SectionData::fromArray([
            'grade_level_id' => $this->gradeLevel->id,
            'name' => 'Section A',
            'code' => 'A',
        ]);

        $this->service->create($data);

        Event::assertDispatched(SectionCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create();

        Event::fake([SectionUpdated::class]);

        $this->service->update($section, ['name' => 'Updated']);

        Event::assertDispatched(SectionUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->active()
            ->create();

        Event::fake([SectionArchived::class]);

        $this->service->archive($section);

        Event::assertDispatched(SectionArchived::class);
    }

    public function test_can_find_by_id(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create();

        $found = $this->service->findById($section->id);

        $this->assertEquals($section->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_grade_level(): void
    {
        Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A']);

        $section2 = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section B', 'code' => 'B']);

        $this->expectException(ValidationException::class);

        $this->service->update($section2, ['name' => 'Section A']);
    }

    public function test_can_update_same_section_name(): void
    {
        $section = Section::factory()
            ->forGradeLevel($this->gradeLevel->id)
            ->create(['name' => 'Section A', 'code' => 'A']);

        $updated = $this->service->update($section, ['description' => 'Updated']);

        $this->assertEquals('Section A', $updated->name);
        $this->assertEquals('Updated', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = SectionData::fromArray([
            'grade_level_id' => $this->gradeLevel->id,
            'name' => 'Section A',
            'code' => 'A',
        ]);

        $section = $this->service->create($data);

        $this->assertNotEmpty($section->uuid);
    }
}
