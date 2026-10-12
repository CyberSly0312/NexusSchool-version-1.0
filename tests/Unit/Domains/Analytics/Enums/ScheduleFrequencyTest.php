<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\ScheduleFrequency;
use Tests\TestCase;

class ScheduleFrequencyTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = ScheduleFrequency::cases();
        $this->assertCount(5, $cases);
        $this->assertSame(ScheduleFrequency::Daily, $cases[0]);
        $this->assertSame(ScheduleFrequency::Weekly, $cases[1]);
        $this->assertSame(ScheduleFrequency::Monthly, $cases[2]);
        $this->assertSame(ScheduleFrequency::Quarterly, $cases[3]);
        $this->assertSame(ScheduleFrequency::Yearly, $cases[4]);
    }

    public function test_label_daily(): void
    {
        $this->assertSame('Quotidien', ScheduleFrequency::Daily->label());
    }

    public function test_label_weekly(): void
    {
        $this->assertSame('Hebdomadaire', ScheduleFrequency::Weekly->label());
    }

    public function test_label_monthly(): void
    {
        $this->assertSame('Mensuel', ScheduleFrequency::Monthly->label());
    }

    public function test_label_quarterly(): void
    {
        $this->assertSame('Trimestriel', ScheduleFrequency::Quarterly->label());
    }

    public function test_label_yearly(): void
    {
        $this->assertSame('Annuel', ScheduleFrequency::Yearly->label());
    }

    public function test_labels(): void
    {
        $labels = ScheduleFrequency::labels();

        $this->assertCount(5, $labels);
        $this->assertArrayHasKey(ScheduleFrequency::Daily->value, $labels);
        $this->assertArrayHasKey(ScheduleFrequency::Weekly->value, $labels);
        $this->assertArrayHasKey(ScheduleFrequency::Monthly->value, $labels);
        $this->assertArrayHasKey(ScheduleFrequency::Quarterly->value, $labels);
        $this->assertArrayHasKey(ScheduleFrequency::Yearly->value, $labels);

        $this->assertSame('Quotidien', $labels[ScheduleFrequency::Daily->value]);
        $this->assertSame('Hebdomadaire', $labels[ScheduleFrequency::Weekly->value]);
        $this->assertSame('Mensuel', $labels[ScheduleFrequency::Monthly->value]);
        $this->assertSame('Trimestriel', $labels[ScheduleFrequency::Quarterly->value]);
        $this->assertSame('Annuel', $labels[ScheduleFrequency::Yearly->value]);
    }
}
