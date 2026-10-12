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
        $this->assertSame('Vue d\'ensemble de l\'école', DashboardType::School->label());
    }

    public function test_label_academic(): void
    {
        $this->assertSame('Tableau de bord académique', DashboardType::Academic->label());
    }

    public function test_label_finance(): void
    {
        $this->assertSame('Tableau de bord financier', DashboardType::Finance->label());
    }

    public function test_label_hr(): void
    {
        $this->assertSame('Tableau de bord RH', DashboardType::HR->label());
    }

    public function test_label_library(): void
    {
        $this->assertSame('Tableau de bord bibliothèque', DashboardType::Library->label());
    }

    public function test_label_transport(): void
    {
        $this->assertSame('Tableau de bord transport', DashboardType::Transport->label());
    }

    public function test_label_custom(): void
    {
        $this->assertSame('Tableau de bord personnalisé', DashboardType::Custom->label());
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

        $this->assertSame('Vue d\'ensemble de l\'école', $labels[DashboardType::School->value]);
        $this->assertSame('Tableau de bord académique', $labels[DashboardType::Academic->value]);
        $this->assertSame('Tableau de bord financier', $labels[DashboardType::Finance->value]);
        $this->assertSame('Tableau de bord RH', $labels[DashboardType::HR->value]);
        $this->assertSame('Tableau de bord bibliothèque', $labels[DashboardType::Library->value]);
        $this->assertSame('Tableau de bord transport', $labels[DashboardType::Transport->value]);
        $this->assertSame('Tableau de bord personnalisé', $labels[DashboardType::Custom->value]);
    }
}
