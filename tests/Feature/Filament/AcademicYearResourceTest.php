<?php

namespace Tests\Feature\Filament;

use App\Domains\School\Models\School;
use App\Models\AcademicYear;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicYearResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_academic_year_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/academic-years')
            ->assertSuccessful();
    }

    public function test_can_render_create_academic_year_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/academic-years/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_academic_year_page(): void
    {
        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(),
            'database' => 'school_test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $school = School::find($schoolId);
        $academicYear = AcademicYear::factory()
            ->forSchool($school)
            ->create();

        $this->actingAs($this->admin)
            ->get('/admin/academic-years/'.$academicYear->id.'/edit')
            ->assertSuccessful();
    }
}
