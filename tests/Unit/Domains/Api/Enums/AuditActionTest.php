<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api\Enums;

use App\Domains\Api\Enums\AuditAction;
use Tests\TestCase;

class AuditActionTest extends TestCase
{
    public function test_token_created_label(): void
    {
        $this->assertEquals('Jeton créé', AuditAction::TokenCreated->label());
    }

    public function test_token_revoked_label(): void
    {
        $this->assertEquals('Jeton révoqué', AuditAction::TokenRevoked->label());
    }

    public function test_client_created_label(): void
    {
        $this->assertEquals('Client créé', AuditAction::ClientCreated->label());
    }

    public function test_client_disabled_label(): void
    {
        $this->assertEquals('Client désactivé', AuditAction::ClientDisabled->label());
    }

    public function test_webhook_registered_label(): void
    {
        $this->assertEquals('Webhook enregistré', AuditAction::WebhookRegistered->label());
    }

    public function test_webhook_delivered_label(): void
    {
        $this->assertEquals('Webhook livré', AuditAction::WebhookDelivered->label());
    }

    public function test_webhook_failed_label(): void
    {
        $this->assertEquals('Webhook échoué', AuditAction::WebhookFailed->label());
    }

    public function test_token_created_color(): void
    {
        $this->assertEquals('info', AuditAction::TokenCreated->color());
    }

    public function test_token_revoked_color(): void
    {
        $this->assertEquals('warning', AuditAction::TokenRevoked->color());
    }

    public function test_client_created_color(): void
    {
        $this->assertEquals('success', AuditAction::ClientCreated->color());
    }

    public function test_client_disabled_color(): void
    {
        $this->assertEquals('danger', AuditAction::ClientDisabled->color());
    }

    public function test_webhook_registered_color(): void
    {
        $this->assertEquals('primary', AuditAction::WebhookRegistered->color());
    }

    public function test_webhook_delivered_color(): void
    {
        $this->assertEquals('success', AuditAction::WebhookDelivered->color());
    }

    public function test_webhook_failed_color(): void
    {
        $this->assertEquals('danger', AuditAction::WebhookFailed->color());
    }

    public function test_labels(): void
    {
        $labels = AuditAction::labels();

        $this->assertCount(7, $labels);
        $this->assertArrayHasKey('token_created', $labels);
        $this->assertArrayHasKey('token_revoked', $labels);
        $this->assertArrayHasKey('client_created', $labels);
        $this->assertArrayHasKey('client_disabled', $labels);
        $this->assertArrayHasKey('webhook_registered', $labels);
        $this->assertArrayHasKey('webhook_delivered', $labels);
        $this->assertArrayHasKey('webhook_failed', $labels);

        $this->assertEquals('Jeton créé', $labels['token_created']);
        $this->assertEquals('Jeton révoqué', $labels['token_revoked']);
        $this->assertEquals('Client créé', $labels['client_created']);
        $this->assertEquals('Client désactivé', $labels['client_disabled']);
        $this->assertEquals('Webhook enregistré', $labels['webhook_registered']);
        $this->assertEquals('Webhook livré', $labels['webhook_delivered']);
        $this->assertEquals('Webhook échoué', $labels['webhook_failed']);
    }
}
