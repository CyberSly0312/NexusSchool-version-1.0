<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\AcademicYearResource;
use App\Filament\Resources\AcademicYearResource\Pages\EditAcademicYear;
use App\Models\AcademicYear;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class AcademicYearResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = (string) Str::ulid();
        DB::table('tenants')->insert([
            'id' => $this->schoolId,
            'uuid' => Str::ulid(),
            'name' => 'Test School',
            'slug' => 'test-school',
            'database' => 'test-school-db',
            'domain' => 'test.nexus.school',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'school_id' => $this->schoolId,
        ]);

        
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
        $this->admin->assignRole($role);
$this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
    }

    public function test_can_render_academic_year_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/academic-years')
            ->assertSuccessful();
    }

    public function test_can_render_create_academic_year_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/academic-years/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_academic_year_page(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->schoolId]);

        $this->actingAs($this->admin)
            ->get('/school-admin/academic-years/'.$academicYear->id.'/edit')
            ->assertSuccessful();
    }

    public function test_can_update_academic_year(): void
    {
        $academicYear = AcademicYear::factory()->create([
            'name' => '2025-2026',
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditAcademicYear::class, ['record' => $academicYear->getRouteKey()])
            ->fillForm([
                'name' => '2025-2026 Updated',
            ])
            ->call('save');

        $this->assertDatabaseHas('academic_years', [
            'id' => $academicYear->id,
            'name' => '2025-2026 Updated',
        ]);
    }

    public function test_can_delete_academic_year(): void
    {
        $academicYear = AcademicYear::factory()->create(['school_id' => $this->schoolId]);

        Livewire::test(EditAcademicYear::class, ['record' => $academicYear->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('academic_years', [
            'id' => $academicYear->id,
        ]);
    }
}
