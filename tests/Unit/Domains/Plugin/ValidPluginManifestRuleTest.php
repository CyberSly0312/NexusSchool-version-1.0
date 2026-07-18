<?php
declare(strict_types=1);

namespace Tests\Unit\Domains\Plugin;

use App\Domains\Plugin\Rules\ValidPluginManifest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

final class ValidPluginManifestRuleTest extends TestCase
{
    public function test_validation_passes_for_valid_manifest(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'plugin_test');
        file_put_contents($tmpFile, json_encode([
            'name' => 'valid-plugin',
            'display_name' => 'Valid Plugin',
            'version' => '1.0.0',
        ]));

        $validator = Validator::make(
            ['manifest' => $tmpFile],
            ['manifest' => [new ValidPluginManifest()]]
        );

        $this->assertFalse($validator->fails());
        @unlink($tmpFile);
    }

    public function test_validation_fails_for_nonexistent_file(): void
    {
        $validator = Validator::make(
            ['manifest' => '/nonexistent/file.json'],
            ['manifest' => [new ValidPluginManifest()]]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_validation_fails_for_invalid_json(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'plugin_test');
        file_put_contents($tmpFile, 'not json');

        $validator = Validator::make(
            ['manifest' => $tmpFile],
            ['manifest' => [new ValidPluginManifest()]]
        );

        $this->assertTrue($validator->fails());
        @unlink($tmpFile);
    }

    public function test_validation_fails_for_missing_name(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'plugin_test');
        file_put_contents($tmpFile, json_encode([
            'display_name' => 'No Name Plugin',
            'version' => '1.0.0',
        ]));

        $validator = Validator::make(
            ['manifest' => $tmpFile],
            ['manifest' => [new ValidPluginManifest()]]
        );

        $this->assertTrue($validator->fails());
        @unlink($tmpFile);
    }

    public function test_validation_fails_for_invalid_name_format(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'plugin_test');
        file_put_contents($tmpFile, json_encode([
            'name' => 'Invalid Name!',
            'display_name' => 'Invalid',
            'version' => '1.0.0',
        ]));

        $validator = Validator::make(
            ['manifest' => $tmpFile],
            ['manifest' => [new ValidPluginManifest()]]
        );

        $this->assertTrue($validator->fails());
        @unlink($tmpFile);
    }
}
