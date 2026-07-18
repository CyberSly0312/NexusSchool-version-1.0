<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\SnapshotStatus;
use Tests\TestCase;

class SnapshotStatusTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = SnapshotStatus::cases();
        $this->assertCount(3, $cases);
        $this->assertSame(SnapshotStatus::Pending, $cases[0]);
        $this->assertSame(SnapshotStatus::Generated, $cases[1]);
        $this->assertSame(SnapshotStatus::Failed, $cases[2]);
    }

    public function test_label_pending(): void
    {
        $this->assertSame('Pending', SnapshotStatus::Pending->label());
    }

    public function test_label_generated(): void
    {
        $this->assertSame('Generated', SnapshotStatus::Generated->label());
    }

    public function test_label_failed(): void
    {
        $this->assertSame('Failed', SnapshotStatus::Failed->label());
    }

    public function test_color_pending(): void
    {
        $this->assertSame('warning', SnapshotStatus::Pending->color());
    }

    public function test_color_generated(): void
    {
        $this->assertSame('success', SnapshotStatus::Generated->color());
    }

    public function test_color_failed(): void
    {
        $this->assertSame('danger', SnapshotStatus::Failed->color());
    }

    public function test_labels(): void
    {
        $labels = SnapshotStatus::labels();

        $this->assertCount(3, $labels);
        $this->assertArrayHasKey(SnapshotStatus::Pending->value, $labels);
        $this->assertArrayHasKey(SnapshotStatus::Generated->value, $labels);
        $this->assertArrayHasKey(SnapshotStatus::Failed->value, $labels);

        $this->assertSame('Pending', $labels[SnapshotStatus::Pending->value]);
        $this->assertSame('Generated', $labels[SnapshotStatus::Generated->value]);
        $this->assertSame('Failed', $labels[SnapshotStatus::Failed->value]);
    }
}
