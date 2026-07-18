<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Workflow;

use App\Filament\Resources\WorkflowResource;
use App\Filament\Resources\WorkflowResource\Pages\EditWorkflow;
use App\Models\User;
use App\Models\Workflow;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class WorkflowResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolId = (string) Str::ulid();
        \DB::table('tenants')->insert([
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

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_list_workflows(): void
    {
        Workflow::factory()->count(3)->create([
            'school_id' => $this->schoolId,
        ]);

        $this->get(WorkflowResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_form(): void
    {
        $this->get(WorkflowResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_form(): void
    {
        $workflow = Workflow::factory()->create([
            'school_id' => $this->schoolId,
        ]);

        $this->get(WorkflowResource::getUrl('edit', ['record' => $workflow]))
            ->assertSuccessful();
    }

    public function test_can_update_workflow(): void
    {
        $workflow = Workflow::factory()->create([
            'name' => 'Original Name',
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditWorkflow::class, ['record' => $workflow->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save');

        $this->assertDatabaseHas('workflows', [
            'id' => $workflow->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_workflow(): void
    {
        $workflow = Workflow::factory()->create([
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditWorkflow::class, ['record' => $workflow->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('workflows', [
            'id' => $workflow->id,
        ]);
    }
}
