<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\AcademicPolicy\Application\DTOs\AcademicPolicyData;
use App\Domains\AcademicPolicy\Application\Services\AcademicPolicyService;
use App\Domains\AcademicPolicy\Domain\Enums\AcademicPolicyValueType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicPolicyResourceTest extends TestCase
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

    public function test_can_render_academic_policies_list_page(): void
    {
        $this->get('/school-admin/academic-policies')
            ->assertSuccessful();
    }

    public function test_can_render_academic_policies_create_page(): void
    {
        $this->get('/school-admin/academic-policies/create')
            ->assertSuccessful();
    }

    public function test_can_render_academic_policies_edit_page(): void
    {
        $policy = app(AcademicPolicyService::class)->create(AcademicPolicyData::fromArray([
            'school_id' => $this->admin->school_id,
            'key' => 'grading_scale',
            'label' => 'Échelle de notation',
            'value_type' => AcademicPolicyValueType::Float->value,
            'value' => 20.0,
            'scope' => 'school',
        ]));

        $this->get('/school-admin/academic-policies/'.$policy->getKey().'/edit')
            ->assertSuccessful();
    }
}
