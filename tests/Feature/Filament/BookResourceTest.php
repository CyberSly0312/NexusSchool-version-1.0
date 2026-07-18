<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\BookResource;
use App\Filament\Resources\BookResource\Pages\EditBook;
use App\Models\Book;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class BookResourceTest extends TestCase
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

        $this->admin = User::factory()->create(['status' => 'active', 'school_id' => $this->schoolId]);
        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_book_list_page(): void
    {
        $this->get(route('filament.admin.resources.books.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_book_page(): void
    {
        $this->get(route('filament.admin.resources.books.create'))
            ->assertSuccessful();
    }

    public function test_can_render_edit_book_page(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);

        $this->get(route('filament.admin.resources.books.edit', ['record' => $book]))
            ->assertSuccessful();
    }

    public function test_can_update_book(): void
    {
        $book = Book::factory()->create([
            'title' => 'Original Title',
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditBook::class, ['record' => $book->getRouteKey()])
            ->fillForm([
                'title' => 'Updated Title',
            ])
            ->call('save');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_can_delete_book(): void
    {
        $book = Book::factory()->create(['school_id' => $this->schoolId]);

        Livewire::test(EditBook::class, ['record' => $book->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('books', [
            'id' => $book->id,
        ]);
    }
}
