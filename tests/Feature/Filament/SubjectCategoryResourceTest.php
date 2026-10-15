<?php

namespace Tests\Feature\Filament;

use App\Models\SubjectCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubjectCategoryResourceTest extends TestCase
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

    public function test_can_render_subject_category_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/subject-categories')
            ->assertSuccessful();
    }

    public function test_can_render_create_subject_category_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/subject-categories/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_subject_category_page(): void
    {
        $subjectCategory = SubjectCategory::factory()->create(['school_id' => $this->admin->school_id]);

        $this->actingAs($this->admin)
            ->get('/school-admin/subject-categories/'.$subjectCategory->id.'/edit')
            ->assertSuccessful();
    }
}
