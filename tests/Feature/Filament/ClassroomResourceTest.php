<?php

namespace Tests\Feature\Filament;

use App\Models\Classroom;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassroomResourceTest extends TestCase
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

    public function test_can_render_classroom_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/classrooms')
            ->assertSuccessful();
    }

    public function test_can_render_create_classroom_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/classrooms/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_classroom_page(): void
    {
        $classroom = Classroom::factory()->create();

        $this->actingAs($this->admin)
            ->get('/admin/classrooms/'.$classroom->id.'/edit')
            ->assertSuccessful();
    }
}
