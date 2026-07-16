<?php

namespace Tests\Feature\Filament;

use App\Models\SubjectCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        ]);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_subject_category_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/subject-categories')
            ->assertSuccessful();
    }

    public function test_can_render_create_subject_category_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/subject-categories/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_subject_category_page(): void
    {
        $subjectCategory = SubjectCategory::factory()->create();

        $this->actingAs($this->admin)
            ->get('/admin/subject-categories/'.$subjectCategory->id.'/edit')
            ->assertSuccessful();
    }
}
