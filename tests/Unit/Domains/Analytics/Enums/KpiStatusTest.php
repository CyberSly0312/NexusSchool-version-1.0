<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\KpiStatus;
use Tests\TestCase;

class KpiStatusTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = KpiStatus::cases();
        $this->assertCount(3, $cases);
        $this->assertSame(KpiStatus::Active, $cases[0]);
        $this->assertSame(KpiStatus::Inactive, $cases[1]);
        $this->assertSame(KpiStatus::Archived, $cases[2]);
    }

    public function test_label_active(): void
    {
        $this->assertSame('Actif', KpiStatus::Active->label());
    }

    public function test_label_inactive(): void
    {
        $this->assertSame('Inactif', KpiStatus::Inactive->label());
    }

    public function test_label_archived(): void
    {
        $this->assertSame('Archivé', KpiStatus::Archived->label());
    }

    public function test_color_active(): void
    {
        $this->assertSame('success', KpiStatus::Active->color());
    }

    public function test_color_inactive(): void
    {
        $this->assertSame('gray', KpiStatus::Inactive->color());
    }

    public function test_color_archived(): void
    {
        $this->assertSame('danger', KpiStatus::Archived->color());
    }

    public function test_labels(): void
    {
        $labels = KpiStatus::labels();

        $this->assertCount(3, $labels);
        $this->assertArrayHasKey(KpiStatus::Active->value, $labels);
        $this->assertArrayHasKey(KpiStatus::Inactive->value, $labels);
        $this->assertArrayHasKey(KpiStatus::Archived->value, $labels);

        $this->assertSame('Actif', $labels[KpiStatus::Active->value]);
        $this->assertSame('Inactif', $labels[KpiStatus::Inactive->value]);
        $this->assertSame('Archivé', $labels[KpiStatus::Archived->value]);
    }
}
