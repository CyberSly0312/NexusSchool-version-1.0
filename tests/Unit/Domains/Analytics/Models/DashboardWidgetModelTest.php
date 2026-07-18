<?php

namespace Tests\Unit\Domains\Analytics\Models;

use App\Domains\Analytics\Enums\WidgetType;
use App\Models\Dashboard;
use App\Models\DashboardWidget;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardWidgetModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('PRAGMA foreign_keys = OFF');
    }

    public function test_boot_generates_ulid_when_id_is_empty(): void
    {
        $widget = DashboardWidget::factory()->create(['id' => null]);

        $this->assertNotNull($widget->id);
        $this->assertEquals(26, strlen($widget->id));
    }

    public function test_widget_type_casts_to_widget_type_enum(): void
    {
        $widget = DashboardWidget::factory()->create(['widget_type' => 'chart']);

        $this->assertInstanceOf(WidgetType::class, $widget->widget_type);
        $this->assertEquals(WidgetType::Chart, $widget->widget_type);
    }

    public function test_config_casts_to_array(): void
    {
        $widget = DashboardWidget::factory()->create(['config' => ['color' => 'blue', 'size' => 'large']]);

        $this->assertIsArray($widget->config);
        $this->assertEquals('blue', $widget->config['color']);
    }

    public function test_is_active_casts_to_boolean(): void
    {
        $widget = DashboardWidget::factory()->create(['is_active' => false]);

        $this->assertIsBool($widget->is_active);
        $this->assertFalse($widget->is_active);
    }

    public function test_has_dashboard_relationship(): void
    {
        $widget = DashboardWidget::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $widget->dashboard());
    }

    public function test_has_school_relationship(): void
    {
        $widget = DashboardWidget::factory()->create(['school_id' => 'school-1']);

        $this->assertInstanceOf(BelongsTo::class, $widget->school());
    }

    public function test_scope_active_filters_correctly(): void
    {
        DashboardWidget::factory()->create(['is_active' => true]);
        DashboardWidget::factory()->create(['is_active' => false]);

        $active = DashboardWidget::active()->get();

        $this->assertCount(1, $active);
        $this->assertTrue($active->first()->is_active);
    }

    public function test_scope_for_dashboard_filters_correctly(): void
    {
        $dashboard = Dashboard::factory()->create();

        DashboardWidget::factory()->forDashboard($dashboard->id)->create();
        DashboardWidget::factory()->create();

        $results = DashboardWidget::forDashboard($dashboard->id)->get();

        $this->assertCount(1, $results);
        $this->assertEquals($dashboard->id, $results->first()->dashboard_id);
    }

    public function test_scope_ordered_sorts_by_position(): void
    {
        DashboardWidget::factory()->create(['position' => 3]);
        DashboardWidget::factory()->create(['position' => 1]);
        DashboardWidget::factory()->create(['position' => 2]);

        $ordered = DashboardWidget::ordered()->get();

        $this->assertCount(3, $ordered);
        $this->assertEquals(1, $ordered[0]->position);
        $this->assertEquals(2, $ordered[1]->position);
        $this->assertEquals(3, $ordered[2]->position);
    }

    public function test_uses_soft_deletes(): void
    {
        $widget = DashboardWidget::factory()->create();

        $widget->delete();

        $this->assertSoftDeleted('dashboard_widgets', ['id' => $widget->id]);

        $widget->restore();

        $this->assertNotSoftDeleted('dashboard_widgets', ['id' => $widget->id]);
    }
}
