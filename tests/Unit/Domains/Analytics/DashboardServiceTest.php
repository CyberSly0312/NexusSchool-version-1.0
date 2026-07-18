<?php

namespace Tests\Unit\Domains\Analytics;

use App\Domains\Analytics\DTOs\DashboardData;
use App\Domains\Analytics\Events\DashboardCreated;
use App\Domains\Analytics\Events\WidgetAdded;
use App\Domains\Analytics\Services\DashboardService;
use App\Models\Dashboard;
use App\Models\DashboardWidget;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;

    protected string $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService;

        $schoolId = 'test-school-'.uniqid();
        DB::table('tenants')->insert([
            'id' => $schoolId, 'uuid' => Str::ulid(), 'name' => 'Test School',
            'slug' => 'test-school-'.uniqid(), 'database' => 'school_test_school',
            'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->schoolId = $schoolId;
    }

    public function test_can_create_dashboard(): void
    {
        $data = [
            'school_id' => $this->schoolId,
            'name' => 'Test Dashboard',
            'type' => 'school',
        ];

        $result = $this->service->create($data);

        $this->assertInstanceOf(DashboardData::class, $result);
        $this->assertEquals('Test Dashboard', $result->name);
        $this->assertEquals('school', $result->type);
        $this->assertEquals($this->schoolId, $result->schoolId);
    }

    public function test_can_update_dashboard(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create(['name' => 'Old Name']);
        $result = $this->service->update($dashboard, ['name' => 'New Name']);
        $this->assertEquals('New Name', $result->name);
    }

    public function test_can_delete_dashboard(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create();
        $this->service->delete($dashboard);
        $this->assertSoftDeleted('dashboards', ['id' => $dashboard->id]);
    }

    public function test_can_find_by_id(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create();
        $found = $this->service->findById($dashboard->id);
        $this->assertEquals($dashboard->id, $found->id);
    }

    public function test_find_by_id_throws_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->findById('non-existent-id');
    }

    public function test_can_paginate_dashboards(): void
    {
        Dashboard::factory()->forSchool($this->schoolId)->count(5)->create();
        $result = $this->service->paginate([], 10);
        $this->assertEquals(5, $result->total());
    }

    public function test_can_add_widget(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create();
        $data = [
            'school_id' => $this->schoolId,
            'title' => 'Test Widget',
            'widget_type' => 'metric',
            'data_source' => 'enrollment_stats',
            'position' => 0,
        ];

        $result = $this->service->addWidget($dashboard->id, $data);

        $this->assertEquals('Test Widget', $result->title);
        $this->assertEquals($dashboard->id, $result->dashboardId);
    }

    public function test_duplicate_dashboard_name_is_rejected(): void
    {
        Dashboard::factory()->forSchool($this->schoolId)->create(['name' => 'Existing Dashboard']);
        $this->expectException(ValidationException::class);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Existing Dashboard',
            'type' => 'school',
        ]);
    }

    public function test_events_dispatched_on_create(): void
    {
        Event::fake([DashboardCreated::class]);
        $this->service->create([
            'school_id' => $this->schoolId,
            'name' => 'Event Dashboard',
            'type' => 'school',
        ]);
        Event::assertDispatched(DashboardCreated::class);
    }

    public function test_can_update_widget(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create();
        $widget = $this->service->addWidget($dashboard->id, [
            'school_id' => $this->schoolId,
            'title' => 'Old Title',
            'widget_type' => 'metric',
            'data_source' => 'enrollment_stats',
            'position' => 0,
        ]);

        $widgetModel = DashboardWidget::find($widget->id);
        $result = $this->service->updateWidget($widgetModel, ['title' => 'New Title']);
        $this->assertEquals('New Title', $result->title);
    }

    public function test_can_remove_widget(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create();
        $widget = $this->service->addWidget($dashboard->id, [
            'school_id' => $this->schoolId,
            'title' => 'Removable Widget',
            'widget_type' => 'metric',
            'data_source' => 'enrollment_stats',
            'position' => 0,
        ]);

        $widgetModel = DashboardWidget::find($widget->id);
        $this->service->removeWidget($widgetModel);
        $this->assertSoftDeleted('dashboard_widgets', ['id' => $widget->id]);
    }

    public function test_add_widget_dispatches_widget_added_event(): void
    {
        $dashboard = Dashboard::factory()->forSchool($this->schoolId)->create();
        Event::fake([WidgetAdded::class]);

        $this->service->addWidget($dashboard->id, [
            'school_id' => $this->schoolId,
            'title' => 'Event Widget',
            'widget_type' => 'metric',
            'data_source' => 'enrollment_stats',
            'position' => 0,
        ]);

        Event::assertDispatched(WidgetAdded::class);
    }

    public function test_paginate_filters_by_search_term(): void
    {
        Dashboard::factory()->forSchool($this->schoolId)->create(['name' => 'Revenue Overview']);
        Dashboard::factory()->forSchool($this->schoolId)->create(['name' => 'Student Metrics']);

        $result = $this->service->paginate(['search' => 'Revenue'], 10);
        $this->assertEquals(1, $result->total());
    }

    public function test_paginate_filters_by_type(): void
    {
        Dashboard::factory()->forSchool($this->schoolId)->create(['type' => 'school']);
        Dashboard::factory()->forSchool($this->schoolId)->create(['type' => 'finance']);

        $result = $this->service->paginate(['type' => 'school'], 10);
        $this->assertEquals(1, $result->total());
    }
}
