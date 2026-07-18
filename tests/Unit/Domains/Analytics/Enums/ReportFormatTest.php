<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Analytics\Enums;

use App\Domains\Analytics\Enums\ReportFormat;
use Tests\TestCase;

class ReportFormatTest extends TestCase
{
    public function test_cases(): void
    {
        $cases = ReportFormat::cases();
        $this->assertCount(3, $cases);
        $this->assertSame(ReportFormat::Pdf, $cases[0]);
        $this->assertSame(ReportFormat::Excel, $cases[1]);
        $this->assertSame(ReportFormat::Csv, $cases[2]);
    }

    public function test_label_pdf(): void
    {
        $this->assertSame('PDF', ReportFormat::Pdf->label());
    }

    public function test_label_excel(): void
    {
        $this->assertSame('Excel', ReportFormat::Excel->label());
    }

    public function test_label_csv(): void
    {
        $this->assertSame('CSV', ReportFormat::Csv->label());
    }

    public function test_color_pdf(): void
    {
        $this->assertSame('danger', ReportFormat::Pdf->color());
    }

    public function test_color_excel(): void
    {
        $this->assertSame('success', ReportFormat::Excel->color());
    }

    public function test_color_csv(): void
    {
        $this->assertSame('info', ReportFormat::Csv->color());
    }

    public function test_labels(): void
    {
        $labels = ReportFormat::labels();

        $this->assertCount(3, $labels);
        $this->assertArrayHasKey(ReportFormat::Pdf->value, $labels);
        $this->assertArrayHasKey(ReportFormat::Excel->value, $labels);
        $this->assertArrayHasKey(ReportFormat::Csv->value, $labels);

        $this->assertSame('PDF', $labels[ReportFormat::Pdf->value]);
        $this->assertSame('Excel', $labels[ReportFormat::Excel->value]);
        $this->assertSame('CSV', $labels[ReportFormat::Csv->value]);
    }
}
