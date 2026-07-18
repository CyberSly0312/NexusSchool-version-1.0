<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Api\Enums;

use App\Domains\Api\Enums\ApiTokenType;
use Tests\TestCase;

class ApiTokenTypeTest extends TestCase
{
    public function test_personal_label(): void
    {
        $this->assertEquals('Personal Access Token', ApiTokenType::Personal->label());
    }

    public function test_client_label(): void
    {
        $this->assertEquals('Client Token', ApiTokenType::Client->label());
    }

    public function test_integration_label(): void
    {
        $this->assertEquals('Integration Token', ApiTokenType::Integration->label());
    }

    public function test_labels(): void
    {
        $labels = ApiTokenType::labels();

        $this->assertArrayHasKey('personal', $labels);
        $this->assertArrayHasKey('client', $labels);
        $this->assertArrayHasKey('integration', $labels);
        $this->assertEquals('Personal Access Token', $labels['personal']);
        $this->assertEquals('Client Token', $labels['client']);
        $this->assertEquals('Integration Token', $labels['integration']);
    }
}
