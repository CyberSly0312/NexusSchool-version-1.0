<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\ReportStatus;
use Tests\TestCase;

class ReportStatusTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = ReportStatus::cases();
        $this->assertCount(5, $cases);
        $this->assertSame(ReportStatus::Draft, $cases[0]);
        $this->assertSame(ReportStatus::Scheduled, $cases[1]);
        $this->assertSame(ReportStatus::Generating, $cases[2]);
        $this->assertSame(ReportStatus::Completed, $cases[3]);
        $this->assertSame(ReportStatus::Failed, $cases[4]);
    }

    public function test_label_draft(): void
    {
        $this->assertSame('Draft', ReportStatus::Draft->label());
    }

    public function test_label_scheduled(): void
    {
        $this->assertSame('Scheduled', ReportStatus::Scheduled->label());
    }

    public function test_label_generating(): void
    {
        $this->assertSame('Generating', ReportStatus::Generating->label());
    }

    public function test_label_completed(): void
    {
        $this->assertSame('Completed', ReportStatus::Completed->label());
    }

    public function test_label_failed(): void
    {
        $this->assertSame('Failed', ReportStatus::Failed->label());
    }

    public function test_color_draft(): void
    {
        $this->assertSame('gray', ReportStatus::Draft->color());
    }

    public function test_color_scheduled(): void
    {
        $this->assertSame('info', ReportStatus::Scheduled->color());
    }

    public function test_color_generating(): void
    {
        $this->assertSame('warning', ReportStatus::Generating->color());
    }

    public function test_color_completed(): void
    {
        $this->assertSame('success', ReportStatus::Completed->color());
    }

    public function test_color_failed(): void
    {
        $this->assertSame('danger', ReportStatus::Failed->color());
    }

    public function test_labels(): void
    {
        $labels = ReportStatus::labels();

        $this->assertCount(5, $labels);
        $this->assertArrayHasKey(ReportStatus::Draft->value, $labels);
        $this->assertArrayHasKey(ReportStatus::Scheduled->value, $labels);
        $this->assertArrayHasKey(ReportStatus::Generating->value, $labels);
        $this->assertArrayHasKey(ReportStatus::Completed->value, $labels);
        $this->assertArrayHasKey(ReportStatus::Failed->value, $labels);

        $this->assertSame('Draft', $labels[ReportStatus::Draft->value]);
        $this->assertSame('Scheduled', $labels[ReportStatus::Scheduled->value]);
        $this->assertSame('Generating', $labels[ReportStatus::Generating->value]);
        $this->assertSame('Completed', $labels[ReportStatus::Completed->value]);
        $this->assertSame('Failed', $labels[ReportStatus::Failed->value]);
    }
}
