<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api\Enums;

use App\Domains\Api\Enums\ApiTokenType;
use Tests\TestCase;

class ApiTokenTypeTest extends TestCase
{
    public function test_personal_label(): void
    {
        $this->assertEquals('Jeton d\'accès personnel', ApiTokenType::Personal->label());
    }

    public function test_client_label(): void
    {
        $this->assertEquals('Jeton client', ApiTokenType::Client->label());
    }

    public function test_integration_label(): void
    {
        $this->assertEquals('Jeton d\'intégration', ApiTokenType::Integration->label());
    }

    public function test_labels(): void
    {
        $labels = ApiTokenType::labels();

        $this->assertArrayHasKey('personal', $labels);
        $this->assertArrayHasKey('client', $labels);
        $this->assertArrayHasKey('integration', $labels);
        $this->assertEquals('Jeton d\'accès personnel', $labels['personal']);
        $this->assertEquals('Jeton client', $labels['client']);
        $this->assertEquals('Jeton d\'intégration', $labels['integration']);
    }
}
