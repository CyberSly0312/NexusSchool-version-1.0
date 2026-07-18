<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookLoanResourceTest extends TestCase
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

    public function test_can_render_book_loan_list_page(): void
    {
        $this->get(route('filament.admin.resources.book-loans.index'))
            ->assertSuccessful();
    }

    public function test_can_render_create_book_loan_page(): void
    {
        $this->get(route('filament.admin.resources.book-loans.create'))
            ->assertSuccessful();
    }
}
