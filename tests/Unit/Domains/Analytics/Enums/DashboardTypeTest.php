<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\DashboardType;
use Tests\TestCase;

class DashboardTypeTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = DashboardType::cases();
        $this->assertCount(7, $cases);
        $this->assertSame(DashboardType::School, $cases[0]);
        $this->assertSame(DashboardType::Academic, $cases[1]);
        $this->assertSame(DashboardType::Finance, $cases[2]);
        $this->assertSame(DashboardType::HR, $cases[3]);
        $this->assertSame(DashboardType::Library, $cases[4]);
        $this->assertSame(DashboardType::Transport, $cases[5]);
        $this->assertSame(DashboardType::Custom, $cases[6]);
    }

    public function test_label_school(): void
    {
        $this->assertSame('School Overview', DashboardType::School->label());
    }

    public function test_label_academic(): void
    {
        $this->assertSame('Academic Dashboard', DashboardType::Academic->label());
    }

    public function test_label_finance(): void
    {
        $this->assertSame('Finance Dashboard', DashboardType::Finance->label());
    }

    public function test_label_hr(): void
    {
        $this->assertSame('HR Dashboard', DashboardType::HR->label());
    }

    public function test_label_library(): void
    {
        $this->assertSame('Library Dashboard', DashboardType::Library->label());
    }

    public function test_label_transport(): void
    {
        $this->assertSame('Transport Dashboard', DashboardType::Transport->label());
    }

    public function test_label_custom(): void
    {
        $this->assertSame('Custom Dashboard', DashboardType::Custom->label());
    }

    public function test_color_school(): void
    {
        $this->assertSame('primary', DashboardType::School->color());
    }

    public function test_color_academic(): void
    {
        $this->assertSame('info', DashboardType::Academic->color());
    }

    public function test_color_finance(): void
    {
        $this->assertSame('success', DashboardType::Finance->color());
    }

    public function test_color_hr(): void
    {
        $this->assertSame('warning', DashboardType::HR->color());
    }

    public function test_color_library(): void
    {
        $this->assertSame('gray', DashboardType::Library->color());
    }

    public function test_color_transport(): void
    {
        $this->assertSame('danger', DashboardType::Transport->color());
    }

    public function test_color_custom(): void
    {
        $this->assertSame('secondary', DashboardType::Custom->color());
    }

    public function test_labels(): void
    {
        $labels = DashboardType::labels();

        $this->assertCount(7, $labels);
        $this->assertArrayHasKey(DashboardType::School->value, $labels);
        $this->assertArrayHasKey(DashboardType::Academic->value, $labels);
        $this->assertArrayHasKey(DashboardType::Finance->value, $labels);
        $this->assertArrayHasKey(DashboardType::HR->value, $labels);
        $this->assertArrayHasKey(DashboardType::Library->value, $labels);
        $this->assertArrayHasKey(DashboardType::Transport->value, $labels);
        $this->assertArrayHasKey(DashboardType::Custom->value, $labels);

        $this->assertSame('School Overview', $labels[DashboardType::School->value]);
        $this->assertSame('Academic Dashboard', $labels[DashboardType::Academic->value]);
        $this->assertSame('Finance Dashboard', $labels[DashboardType::Finance->value]);
        $this->assertSame('HR Dashboard', $labels[DashboardType::HR->value]);
        $this->assertSame('Library Dashboard', $labels[DashboardType::Library->value]);
        $this->assertSame('Transport Dashboard', $labels[DashboardType::Transport->value]);
        $this->assertSame('Custom Dashboard', $labels[DashboardType::Custom->value]);
    }
}
