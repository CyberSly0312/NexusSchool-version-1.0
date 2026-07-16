<?php

namespace Tests\Feature\Filament;

use App\Models\Guardian;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuardianResourceTest extends TestCase
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

    public function test_can_render_guardian_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/guardians')
            ->assertSuccessful();
    }

    public function test_can_render_create_guardian_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/guardians/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_guardian_page(): void
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

        $guardian = Guardian::factory()
            ->forSchool($schoolId)
            ->create();

        $this->actingAs($this->admin)
            ->get('/admin/guardians/'.$guardian->id.'/edit')
            ->assertSuccessful();
    }
}
