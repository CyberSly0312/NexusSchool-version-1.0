<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domains\User\Enums\UserStatus;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use App\Models\UserInvitation;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = makeTenant('Test School');

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

        \Stancl\Tenancy\Facades\Tenancy::initialize(\App\Domains\School\Models\School::find($this->schoolId));
    }

    public function test_can_render_user_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/users')
            ->assertSuccessful();
    }

    public function test_user_list_page_displays_users(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'school_id' => $this->schoolId,
        ]);

        $this->actingAs($this->admin)
            ->get('/school-admin/users')
            ->assertSuccessful();
    }

    public function test_can_render_create_user_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/school-admin/users/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_user_page(): void
    {
        $user = User::factory()->create([
            'school_id' => $this->schoolId,
        ]);

        $this->actingAs($this->admin)
            ->get('/school-admin/users/'.$user->getRouteKey().'/edit')
            ->assertSuccessful();
    }

    public function test_can_create_user(): void
    {
        // The school-admin creation form now provisions through an invitation
        // instead of accepting a password and an active status directly.
        \Spatie\Permission\Models\Role::findOrCreate('teacher', 'web');

        Livewire::test(CreateUser::class)
            ->fillForm([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test-user-unique@example.com',
                'phone' => '+33123456789',
                'role' => 'teacher',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::query()->where('email', 'test-user-unique@example.com')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Test',
            'school_id' => $this->schoolId,
            'status' => UserStatus::Pending->value,
        ]);

        $this->assertSame(['teacher'], $user->getRoleNames()->all());
        $this->assertSame(1, UserInvitation::query()->where('user_id', $user->id)->count());
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Original',
            'last_name' => 'Name',
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'first_name' => 'Updated',
                'last_name' => $user->last_name,
                'email' => $user->email,
                'status' => 'active',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
        ]);
    }

    public function test_can_delete_user(): void
    {
        $user = User::factory()->create([
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }
}
