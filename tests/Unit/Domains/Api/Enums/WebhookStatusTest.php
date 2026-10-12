<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api\Enums;

use App\Domains\Api\Enums\WebhookStatus;
use Tests\TestCase;

class WebhookStatusTest extends TestCase
{
    public function test_active_label(): void
    {
        $this->assertEquals('Actif', WebhookStatus::Active->label());
    }

    public function test_inactive_label(): void
    {
        $this->assertEquals('Inactif', WebhookStatus::Inactive->label());
    }

    public function test_failed_label(): void
    {
        $this->assertEquals('Échoué', WebhookStatus::Failed->label());
    }

    public function test_pending_label(): void
    {
        $this->assertEquals('En attente', WebhookStatus::Pending->label());
    }

    public function test_active_color(): void
    {
        $this->assertEquals('success', WebhookStatus::Active->color());
    }

    public function test_inactive_color(): void
    {
        $this->assertEquals('secondary', WebhookStatus::Inactive->color());
    }

    public function test_failed_color(): void
    {
        $this->assertEquals('danger', WebhookStatus::Failed->color());
    }

    public function test_pending_color(): void
    {
        $this->assertEquals('warning', WebhookStatus::Pending->color());
    }

    public function test_labels(): void
    {
        $labels = WebhookStatus::labels();

        $this->assertCount(4, $labels);
        $this->assertArrayHasKey('active', $labels);
        $this->assertArrayHasKey('inactive', $labels);
        $this->assertArrayHasKey('failed', $labels);
        $this->assertArrayHasKey('pending', $labels);

        $this->assertEquals('Actif', $labels['active']);
        $this->assertEquals('Inactif', $labels['inactive']);
        $this->assertEquals('Échoué', $labels['failed']);
        $this->assertEquals('En attente', $labels['pending']);
    }
}
