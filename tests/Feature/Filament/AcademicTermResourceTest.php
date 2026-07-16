<?php

namespace Tests\Feature\Filament;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicTermResourceTest extends TestCase
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

    public function test_can_render_academic_term_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/academic-terms')
            ->assertSuccessful();
    }

    public function test_can_render_create_academic_term_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/academic-terms/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_academic_term_page(): void
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

        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $schoolId])
            ->create();

        $term = AcademicTerm::factory()
            ->forAcademicYear($academicYear)
            ->create();

        $this->actingAs($this->admin)
            ->get('/admin/academic-terms/'.$term->id.'/edit')
            ->assertSuccessful();
    }
}
