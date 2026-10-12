<?php

namespace Tests\Feature\Filament;

use App\Models\Message;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class MessageResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active', 'school_id' => (string) Str::ulid()]);
        
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
        $this->admin->assignRole($role);
$this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('school-admin');
    }

    public function test_can_render_messages_list_page(): void
    {
        $this->get(route('filament.school-admin.resources.messages.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_message_page(): void
    {
        $this->get(route('filament.school-admin.resources.messages.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_message_page(): void
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

        $message = Message::factory()->create([
            'school_id' => $schoolId,
            'sender_id' => $this->admin->id,
        ]);

        $this->get(route('filament.school-admin.resources.messages.edit', ['record' => $message]))
            ->assertSuccessful();
    }
}
