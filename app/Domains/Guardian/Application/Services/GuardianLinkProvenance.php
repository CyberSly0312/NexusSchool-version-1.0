<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Application\Services;

use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationshipHistory;
use App\Models\User;
use RuntimeException;

/**
 * Server-side attestation for the human-authorized `linked` history record.
 *
 * `issueFor()` is called only by the successful durable-link action. The event
 * must consume that one-time issuance in the same request, so a directly
 * constructed `GuardianLinked` event cannot mint trusted provenance. The stored
 * signature is bound to school, relationship, student, guardian and the
 * authenticated actor. Verification also requires that actor to be active,
 * same-school and `guardian.link`-authorized (or an approved Super Admin).
 */
final class GuardianLinkProvenance
{
    private const VERSION = 1;

    private const EVENT = 'linked';

    /**
     * @var array<int, array{user_id: int, canonical: string}>
     */
    private static array $pending = [];

    public static function issueFor(StudentGuardianRelationship $relationship, User $actor): void
    {
        self::$pending[spl_object_id($relationship)] = [
            'user_id' => (int) $actor->getKey(),
            'canonical' => self::canonical(
                (string) $relationship->school_id,
                (string) $relationship->getKey(),
                (string) $relationship->student_id,
                (string) $relationship->guardian_id,
                (string) $actor->getKey(),
            ),
        ];
    }

    /**
     * @return array{provenance_version: int, provenance_signature: string}
     */
    public static function takeFor(StudentGuardianRelationship $relationship, User $actor): array
    {
        $key = spl_object_id($relationship);
        $pending = self::$pending[$key] ?? null;
        unset(self::$pending[$key]);

        $canonical = self::canonical(
            (string) $relationship->school_id,
            (string) $relationship->getKey(),
            (string) $relationship->student_id,
            (string) $relationship->guardian_id,
            (string) $actor->getKey(),
        );

        if ($pending === null
            || $pending['user_id'] !== (int) $actor->getKey()
            || $pending['canonical'] !== $canonical) {
            throw GuardianRelationshipVerificationException::because(
                GuardianAccessDenialReason::RelationshipProvenanceMissing,
            );
        }

        return [
            'provenance_version' => self::VERSION,
            'provenance_signature' => hash_hmac('sha256', $canonical, self::key()),
        ];
    }

    public static function isTrustedHistory(StudentGuardianRelationshipHistory $history): bool
    {
        if ($history->event !== self::EVENT) {
            return false;
        }

        $rawPayload = $history->getRawOriginal('payload');

        if (! is_string($rawPayload)) {
            return false;
        }

        $payload = json_decode($rawPayload, true);

        if (! is_array($payload)
            || ($payload['provenance_version'] ?? null) !== self::VERSION
            || ! is_string($payload['provenance_signature'] ?? null)) {
            return false;
        }

        if (! $history->school_id
            || ! $history->student_guardian_id
            || ! $history->student_id
            || ! $history->guardian_id
            || ! $history->user_id) {
            return false;
        }

        $expected = hash_hmac('sha256', self::canonical(
            (string) $history->school_id,
            (string) $history->student_guardian_id,
            (string) $history->student_id,
            (string) $history->guardian_id,
            (string) $history->user_id,
        ), self::key());

        if (! hash_equals($expected, $payload['provenance_signature'])) {
            return false;
        }

        $actor = User::withTrashed()->whereKey($history->user_id)->first();

        if (! $actor instanceof User
            || $actor->trashed()
            || ! $actor->isActive()
            || (string) $actor->school_id !== (string) $history->school_id) {
            return false;
        }

        return $actor->hasRole('super_admin')
            || $actor->getAllPermissions()->contains(
                fn ($permission): bool => $permission->name === 'guardian.link',
            );
    }

    private static function canonical(
        string $schoolId,
        string $relationshipId,
        string $studentId,
        string $guardianId,
        string $actorId,
    ): string {
        return implode('|', [
            'guardian-link-provenance',
            (string) self::VERSION,
            self::EVENT,
            $schoolId,
            $relationshipId,
            $studentId,
            $guardianId,
            $actorId,
        ]);
    }

    private static function key(): string
    {
        $key = (string) config('app.key');

        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);

            if ($decoded === false || $decoded === '') {
                throw new RuntimeException('The application key is not a valid base64 key.');
            }

            return $decoded;
        }

        if ($key === '') {
            throw new RuntimeException('The application key is missing.');
        }

        return $key;
    }
}
