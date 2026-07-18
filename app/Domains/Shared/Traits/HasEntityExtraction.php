<?php

declare(strict_types=1);

namespace App\Domains\Shared\Traits;

trait HasEntityExtraction
{
    private function extractEntity(object $event): object
    {
        $reflection = new \ReflectionClass($event);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_READONLY);
        foreach ($properties as $property) {
            $value = $property->getValue($event);
            if (is_object($value) && property_exists($value, 'id')) {
                return $value;
            }
        }
        return $event;
    }
}
