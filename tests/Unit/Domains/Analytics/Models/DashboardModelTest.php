<?php

namespace Tests\Unit\Domains\Analytics\Models;

use App\Domains\Analytics\Enums\DashboardType;
use App\Models\Dashboard;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('PRAGMA foreign_keys = OFF');
    }

    public function test_boot_generates_ulid_when_id_is_empty(): void
    {
        $dashboard = Dashboard::factory()->create(['id' => null]);

        $this->assertNotNull($dashboard->id);
        $this->assertEquals(26, strlen($dashboard->id));
    }

    public function test_boot_auto_generates_slug_from_name(): void
    {
        $dashboard = Dashboard::factory()->create(['name' => 'My Dashboard', 'slug' => null]);

        $this->assertEquals('my-dashboard', $dashboard->slug);
    }

    public function test_type_casts_to_dashboard_type_enum(): void
    {
        $dashboard = Dashboard::factory()->create(['type' => 'school']);

        $this->assertInstanceOf(DashboardType::class, $dashboard->type);
        $this->assertEquals(DashboardType::School, $dashboard->type);
    }

    public function test_layout_casts_to_array(): void
    {
        $dashboard = Dashboard::factory()->create(['layout' => ['cols' => 3]]);

        $this->assertIsArray($dashboard->layout);
        $this->assertEquals(3, $dashboard->layout['cols']);
    }

    public function test_is_default_casts_to_boolean(): void
    {
        $dashboard = Dashboard::factory()->create(['is_default' => true]);

        $this->assertIsBool($dashboard->is_default);
        $this->assertTrue($dashboard->is_default);
    }

    public function test_is_active_casts_to_boolean(): void
    {
        $dashboard = Dashboard::factory()->create(['is_active' => false]);

        $this->assertIsBool($dashboard->is_active);
        $this->assertFalse($dashboard->is_active);
    }

    public function test_has_school_relationship(): void
    {
        $dashboard = Dashboard::factory()->create(['school_id' => 'school-1']);

        $this->assertInstanceOf(BelongsTo::class, $dashboard->school());
    }

    public function test_has_widgets_relationship(): void
    {
        $dashboard = Dashboard::factory()->create();

        $this->assertInstanceOf(HasMany::class, $dashboard->widgets());
    }

    public function test_scope_active_filters_correctly(): void
    {
        Dashboard::factory()->create(['is_active' => true]);
        Dashboard::factory()->create(['is_active' => false]);

        $active = Dashboard::active()->get();

        $this->assertCount(1, $active);
        $this->assertTrue($active->first()->is_active);
    }

    public function test_scope_for_school_filters_correctly(): void
    {
        Dashboard::factory()->forSchool('school-1')->create();
        Dashboard::factory()->forSchool('school-2')->create();

        $results = Dashboard::forSchool('school-1')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('school-1', $results->first()->school_id);
    }

    public function test_scope_by_type_filters_correctly(): void
    {
        Dashboard::factory()->create(['type' => 'school']);
        Dashboard::factory()->create(['type' => 'finance']);

        $results = Dashboard::byType('school')->get();

        $this->assertCount(1, $results);
    }

    public function test_uses_soft_deletes(): void
    {
        $dashboard = Dashboard::factory()->create();

        $dashboard->delete();

        $this->assertSoftDeleted('dashboards', ['id' => $dashboard->id]);

        $dashboard->restore();

        $this->assertNotSoftDeleted('dashboards', ['id' => $dashboard->id]);
    }
}
