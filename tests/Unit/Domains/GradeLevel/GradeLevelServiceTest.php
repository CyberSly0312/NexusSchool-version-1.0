<?php

namespace Tests\Unit\Domains\GradeLevel;

use App\Domains\GradeLevel\DTOs\GradeLevelData;
use App\Domains\GradeLevel\Enums\EducationalCycle;
use App\Domains\GradeLevel\Enums\GradeLevelStatus;
use App\Domains\GradeLevel\Events\GradeLevelArchived;
use App\Domains\GradeLevel\Events\GradeLevelCreated;
use App\Domains\GradeLevel\Events\GradeLevelUpdated;
use App\Domains\GradeLevel\Services\GradeLevelService;
use App\Models\GradeLevel;
use App\Models\Program;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GradeLevelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GradeLevelService $service;

    protected string $schoolId;

    protected Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new GradeLevelService;

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

        $this->program = Program::factory()
            ->forSchool($schoolId)
            ->create(['name' => 'Junior High School', 'code' => 'JHS']);
    }

    public function test_can_create_grade_level(): void
    {
        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '6ème',
            'code' => '6E',
            'short_name' => '6ème',
            'level_order' => 1,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
            'description' => 'First year of junior secondary',
        ]);

        $gradeLevel = $this->service->create($data);

        $this->assertInstanceOf(GradeLevel::class, $gradeLevel);
        $this->assertEquals('6ème', $gradeLevel->name);
        $this->assertEquals('6E', $gradeLevel->code);
        $this->assertEquals('6ème', $gradeLevel->short_name);
        $this->assertEquals(1, $gradeLevel->level_order);
        $this->assertEquals(EducationalCycle::JuniorSecondary, $gradeLevel->educational_cycle);
        $this->assertEquals(GradeLevelStatus::Active, $gradeLevel->status);
    }

    public function test_can_update_grade_level(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => 'Old Name']);

        $updated = $this->service->update($gradeLevel, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_grade_level(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create();

        $deleted = $this->service->delete($gradeLevel);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('grade_levels', ['id' => $gradeLevel->id]);
    }

    public function test_can_archive_grade_level(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->active()
            ->create();

        $archived = $this->service->archive($gradeLevel);

        $this->assertEquals(GradeLevelStatus::Archived, $archived->status);
    }

    public function test_can_restore_grade_level(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->archived()
            ->create();

        $restored = $this->service->restore($gradeLevel);

        $this->assertEquals(GradeLevelStatus::Active, $restored->status);
    }

    public function test_duplicate_name_same_program_is_rejected(): void
    {
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);

        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '6ème',
            'code' => '6E2',
            'level_order' => 2,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_code_same_program_is_rejected(): void
    {
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);

        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '5ème',
            'code' => '6E',
            'level_order' => 2,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_duplicate_level_order_same_program_is_rejected(): void
    {
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);

        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '5ème',
            'code' => '5E',
            'level_order' => 1,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->create($data);
    }

    public function test_same_name_allowed_in_different_programs(): void
    {
        $otherProgram = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High School', 'code' => 'SHS']);

        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => 'Level 1', 'code' => 'L1']);

        $data = GradeLevelData::fromArray([
            'program_id' => $otherProgram->id,
            'name' => 'Level 1',
            'code' => 'L1',
            'level_order' => 1,
            'educational_cycle' => EducationalCycle::SeniorSecondary->value,
        ]);

        $gradeLevel = $this->service->create($data);

        $this->assertNotNull($gradeLevel);
        $this->assertEquals('Level 1', $gradeLevel->name);
    }

    public function test_can_paginate_grade_levels(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            GradeLevel::factory()
                ->forProgram($this->program->id)
                ->withOrder($i)
                ->create();
        }

        $result = $this->service->paginate([], 10);

        $this->assertEquals(5, $result->total());
    }

    public function test_can_filter_by_status(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            GradeLevel::factory()
                ->forProgram($this->program->id)
                ->active()
                ->withOrder($i)
                ->create();
        }
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->archived()
            ->withOrder(4)
            ->create();

        $result = $this->service->paginate(['status' => GradeLevelStatus::Active->value], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_search_by_name(): void
    {
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème']);
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(2)
            ->create(['name' => '5ème']);

        $result = $this->service->paginate(['search' => '6ème'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_search_by_code(): void
    {
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(2)
            ->create(['name' => '5ème', 'code' => '5E']);

        $result = $this->service->paginate(['search' => '6E'], 10);

        $this->assertEquals(1, $result->total());
    }

    public function test_can_filter_by_program(): void
    {
        $otherProgram = Program::factory()
            ->forSchool($this->schoolId)
            ->create(['name' => 'Senior High School', 'code' => 'SHS']);

        for ($i = 1; $i <= 3; $i++) {
            GradeLevel::factory()
                ->forProgram($this->program->id)
                ->withOrder($i)
                ->create();
        }
        for ($i = 1; $i <= 2; $i++) {
            GradeLevel::factory()
                ->forProgram($otherProgram->id)
                ->withOrder($i)
                ->create();
        }

        $result = $this->service->paginate(['program_id' => $this->program->id], 10);

        $this->assertEquals(3, $result->total());
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([GradeLevelCreated::class]);

        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '6ème',
            'code' => '6E',
            'level_order' => 1,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
        ]);

        $this->service->create($data);

        Event::assertDispatched(GradeLevelCreated::class);
    }

    public function test_events_dispatched_on_update(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create();

        Event::fake([GradeLevelUpdated::class]);

        $this->service->update($gradeLevel, ['name' => 'Updated Name']);

        Event::assertDispatched(GradeLevelUpdated::class);
    }

    public function test_events_dispatched_on_archive(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->active()
            ->create();

        Event::fake([GradeLevelArchived::class]);

        $this->service->archive($gradeLevel);

        Event::assertDispatched(GradeLevelArchived::class);
    }

    public function test_can_find_by_id(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create();

        $found = $this->service->findById($gradeLevel->id);

        $this->assertEquals($gradeLevel->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->findById(99999);
    }

    public function test_update_validates_uniqueness_per_program(): void
    {
        GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);

        $gl2 = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(2)
            ->create(['name' => '5ème', 'code' => '5E']);

        $this->expectException(ValidationException::class);

        $this->service->update($gl2, ['name' => '6ème']);
    }

    public function test_can_update_same_grade_level_name(): void
    {
        $gradeLevel = GradeLevel::factory()
            ->forProgram($this->program->id)
            ->withOrder(1)
            ->create(['name' => '6ème', 'code' => '6E']);

        $updated = $this->service->update($gradeLevel, ['description' => 'Updated description']);

        $this->assertEquals('6ème', $updated->name);
        $this->assertEquals('Updated description', $updated->description);
    }

    public function test_uuid_is_auto_generated(): void
    {
        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '6ème',
            'code' => '6E',
            'level_order' => 1,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
        ]);

        $gradeLevel = $this->service->create($data);

        $this->assertNotEmpty($gradeLevel->uuid);
    }

    public function test_educational_cycle_is_cast_to_enum(): void
    {
        $data = GradeLevelData::fromArray([
            'program_id' => $this->program->id,
            'name' => '6ème',
            'code' => '6E',
            'level_order' => 1,
            'educational_cycle' => EducationalCycle::JuniorSecondary->value,
        ]);

        $gradeLevel = $this->service->create($data);

        $this->assertInstanceOf(EducationalCycle::class, $gradeLevel->educational_cycle);
    }
}
