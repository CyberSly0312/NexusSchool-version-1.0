<?php

declare(strict_types=1);

namespace App\Domains\Api\Enums;

enum AuditAction: string
{
    case TokenCreated = 'token_created';
    case TokenRevoked = 'token_revoked';
    case ClientCreated = 'client_created';
    case ClientDisabled = 'client_disabled';
    case WebhookRegistered = 'webhook_registered';
    case WebhookDelivered = 'webhook_delivered';
    case WebhookFailed = 'webhook_failed';

    public function label(): string
    {
        return match ($this) {
            self::TokenCreated => 'Jeton créé',
            self::TokenRevoked => 'Jeton révoqué',
            self::ClientCreated => 'Client créé',
            self::ClientDisabled => 'Client désactivé',
            self::WebhookRegistered => 'Webhook enregistré',
            self::WebhookDelivered => 'Webhook livré',
            self::WebhookFailed => 'Webhook échoué',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TokenCreated => 'info',
            self::TokenRevoked => 'warning',
            self::ClientCreated => 'success',
            self::ClientDisabled => 'danger',
            self::WebhookRegistered => 'primary',
            self::WebhookDelivered => 'success',
            self::WebhookFailed => 'danger',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
