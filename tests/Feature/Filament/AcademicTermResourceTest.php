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
            'school_id' => makeTenant(),
        ]);

        
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
        $this->admin->assignRole($role);
$this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
    }

    public function test_can_render_academic_term_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/academic-terms')
            ->assertSuccessful();
    }

    public function test_can_render_create_academic_term_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/academic-terms/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_academic_term_page(): void
    {
        $schoolId = $this->admin->school_id;

        $academicYear = AcademicYear::factory()
            ->state(['school_id' => $schoolId])
            ->create();

        $term = AcademicTerm::factory()
            ->forAcademicYear($academicYear)
            ->create(['school_id' => $schoolId]);

        $this->actingAs($this->admin)
            ->get('/school-admin/academic-terms/'.$term->id.'/edit')
            ->assertSuccessful();
    }
}
