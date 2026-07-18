<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidPluginManifest implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The plugin manifest path must be a string.');
            return;
        }

        if (!file_exists($value)) {
            $fail("The plugin manifest file does not exist at: {$value}");
            return;
        }

        $contents = file_get_contents($value);
        $data = json_decode($contents, true);

        if (!is_array($data)) {
            $fail('The plugin manifest must be a valid JSON file.');
            return;
        }

        $required = ['name', 'version', 'display_name'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $fail("The plugin manifest must define a '{$field}'.");
                return;
            }
        }

        if (!preg_match('/^[a-z0-9\-]+$/', $data['name'])) {
            $fail('The plugin name must contain only lowercase letters, numbers, and hyphens.');
            return;
        }

        if (!preg_match('/^\d+\.\d+\.\d+$/', $data['version'])) {
            $fail('The plugin version must follow semantic versioning (x.y.z).');
            return;
        }
    }
}
