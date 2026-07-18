<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\WidgetType;
use Tests\TestCase;

class WidgetTypeTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = WidgetType::cases();
        $this->assertCount(6, $cases);
        $this->assertSame(WidgetType::Chart, $cases[0]);
        $this->assertSame(WidgetType::Metric, $cases[1]);
        $this->assertSame(WidgetType::Table, $cases[2]);
        $this->assertSame(WidgetType::Progress, $cases[3]);
        $this->assertSame(WidgetType::List, $cases[4]);
        $this->assertSame(WidgetType::Custom, $cases[5]);
    }

    public function test_label_chart(): void
    {
        $this->assertSame('Chart', WidgetType::Chart->label());
    }

    public function test_label_metric(): void
    {
        $this->assertSame('Metric Card', WidgetType::Metric->label());
    }

    public function test_label_table(): void
    {
        $this->assertSame('Data Table', WidgetType::Table->label());
    }

    public function test_label_progress(): void
    {
        $this->assertSame('Progress Bar', WidgetType::Progress->label());
    }

    public function test_label_list(): void
    {
        $this->assertSame('List', WidgetType::List->label());
    }

    public function test_label_custom(): void
    {
        $this->assertSame('Custom Widget', WidgetType::Custom->label());
    }

    public function test_color_chart(): void
    {
        $this->assertSame('info', WidgetType::Chart->color());
    }

    public function test_color_metric(): void
    {
        $this->assertSame('primary', WidgetType::Metric->color());
    }

    public function test_color_table(): void
    {
        $this->assertSame('gray', WidgetType::Table->color());
    }

    public function test_color_progress(): void
    {
        $this->assertSame('success', WidgetType::Progress->color());
    }

    public function test_color_list(): void
    {
        $this->assertSame('warning', WidgetType::List->color());
    }

    public function test_color_custom(): void
    {
        $this->assertSame('secondary', WidgetType::Custom->color());
    }

    public function test_labels(): void
    {
        $labels = WidgetType::labels();

        $this->assertCount(6, $labels);
        $this->assertArrayHasKey(WidgetType::Chart->value, $labels);
        $this->assertArrayHasKey(WidgetType::Metric->value, $labels);
        $this->assertArrayHasKey(WidgetType::Table->value, $labels);
        $this->assertArrayHasKey(WidgetType::Progress->value, $labels);
        $this->assertArrayHasKey(WidgetType::List->value, $labels);
        $this->assertArrayHasKey(WidgetType::Custom->value, $labels);

        $this->assertSame('Chart', $labels[WidgetType::Chart->value]);
        $this->assertSame('Metric Card', $labels[WidgetType::Metric->value]);
        $this->assertSame('Data Table', $labels[WidgetType::Table->value]);
        $this->assertSame('Progress Bar', $labels[WidgetType::Progress->value]);
        $this->assertSame('List', $labels[WidgetType::List->value]);
        $this->assertSame('Custom Widget', $labels[WidgetType::Custom->value]);
    }
}
