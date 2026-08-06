<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\ValueObjects;

use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use InvalidArgumentException;

/**
 * Configurable visibility rules of a document (SPR-012).
 *
 * Maps every visibility scope to the document sections it may see. Self-scoped
 * scopes (parent, student) always receive their own (or their children's) data
 * only — the engine enforces this at delivery time regardless of the map.
 *
 * Example configuration (JSON, policy-driven):
 *
 *   [
 *     "director"  => ["sections" => ["*"]],
 *     "secretary" => ["sections" => ["administrative"]],
 *     "teacher"   => ["sections" => ["teaching", "student"]],
 *     "parent"    => ["sections" => ["student", "results"], "self_only" => true],
 *     "student"   => ["sections" => ["student", "results"], "self_only" => true],
 *   ]
 */
final readonly class VisibilityPolicy
{
    /**
     * @param  array<string, array{sections: list<string>, self_only?: bool}>  $scopes
     */
    public function __construct(
        public array $scopes = [],
    ) {
    }

    public function sectionsFor(VisibilityScope $scope): array
    {
        $config = $this->scopes[$scope->value] ?? [];

        $sections = $config['sections'] ?? [];

        if (in_array('*', $sections, true)) {
            return ['*'];
        }

        return array_values(array_unique(array_filter(
            $sections,
            static fn (mixed $section): bool => is_string($section) && trim($section) !== '',
        )));
    }

    public function isSelfOnly(VisibilityScope $scope): bool
    {
        return (bool) ($this->scopes[$scope->value]['self_only'] ?? $scope->isSelfScoped());
    }

    public function canSeeSection(VisibilityScope $scope, string $section): bool
    {
        $allowed = $this->sectionsFor($scope);

        return in_array('*', $allowed, true) || in_array($section, $allowed, true);
    }

    public function toArray(): array
    {
        return $this->scopes;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $scopes = [];

        foreach ($data as $key => $config) {
            if (! VisibilityScope::tryFrom((string) $key) instanceof VisibilityScope) {
                throw new InvalidArgumentException("Unknown visibility scope: [{$key}].");
            }

            if (! is_array($config) || ! isset($config['sections']) || ! is_array($config['sections'])) {
                throw new InvalidArgumentException("Visibility policy for [{$key}] must define a section list.");
            }

            $scopes[(string) $key] = [
                'sections' => array_values(array_map('strval', $config['sections'])),
                'self_only' => (bool) ($config['self_only'] ?? VisibilityScope::from($key)->isSelfScoped()),
            ];
        }

        return new self($scopes);
    }
}
