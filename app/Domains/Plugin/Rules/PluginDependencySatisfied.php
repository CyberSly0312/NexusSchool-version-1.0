<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Rules;

use App\Models\Plugin;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class PluginDependencySatisfied implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            return;
        }

        foreach ($value as $dependency) {
            $depName = is_array($dependency) ? ($dependency['name'] ?? '') : $dependency;
            $required = is_array($dependency) ? ($dependency['required'] ?? true) : true;

            if (!$required) {
                continue;
            }

            $exists = Plugin::where('name', $depName)->exists();
            if (!$exists) {
                $fail("Required dependency '{$depName}' is not installed.");
                return;
            }
        }
    }
}
