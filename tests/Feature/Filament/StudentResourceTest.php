<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Pages\EditStudent;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class StudentResourceTest extends TestCase
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

        $this->admin = User::factory()->create([
            'status' => 'active',
            'school_id' => $this->schoolId,
        ]);

        $this->actingAs($this->admin);

        Filament::setServingStatus();
        Filament::setCurrentPanel('admin');
    }

    public function test_can_render_student_list_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/students')
            ->assertSuccessful();
    }

    public function test_can_render_create_student_page(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/students/create')
            ->assertSuccessful();
    }

    public function test_can_render_edit_student_page(): void
    {
        $student = Student::factory()->create(['school_id' => $this->schoolId]);

        $this->actingAs($this->admin)
            ->get('/admin/students/'.$student->id.'/edit')
            ->assertSuccessful();
    }

    public function test_can_update_student(): void
    {
        $student = Student::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'school_id' => $this->schoolId,
        ]);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->fillForm([
                'first_name' => 'Jane Updated',
            ])
            ->call('save');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Jane Updated',
        ]);
    }

    public function test_can_delete_student(): void
    {
        $student = Student::factory()->create(['school_id' => $this->schoolId]);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertSoftDeleted('students', [
            'id' => $student->id,
        ]);
    }
}
