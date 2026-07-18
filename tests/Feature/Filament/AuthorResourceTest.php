<?php

namespace Tests\Feature\Filament;

use App\Models\Author;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_author_list_page(): void
    {
        $this->get(route('filament.admin.resources.authors.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_author_page(): void
    {
        $this->get(route('filament.admin.resources.authors.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_author_page(): void
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

        $author = Author::factory()->create(['school_id' => $schoolId]);

        $this->get(route('filament.admin.resources.authors.edit', ['record' => $author]))
            ->assertSuccessful();
    }
}
