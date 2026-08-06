<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Services;

use App\Domains\AcademicCommunication\Application\DTOs\DocumentRecipientData;
use App\Domains\AcademicCommunication\Domain\Contracts\GuardianContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\Enums\VisibilityScope;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Resolves the concrete recipients of a publication from the policy scopes
 * (SPR-012).
 *
 * Administrative scopes (Director, Secretary, Teacher, Inspector) resolve to
 * the matching staff users of the school. Self-scoped scopes (Parent, Student)
 * resolve to the concrete guardians / the student bound to the document — they
 * can never receive another person's data through this engine. If a policy has
 * no recipients for a scope (e.g. no student bound), the scope yields nothing.
 */
final class PublicationRecipientResolver
{
    public function __construct(
        private readonly GuardianContract $guardian,
        private readonly StudentIdentityContract $studentIdentity,
    ) {
    }

    /**
     * @return list<DocumentRecipientData>
     */
    public function resolve(
        AcademicDocument $document,
        PublicationPolicy $policy,
        string $schoolId,
    ): array {
        $recipients = [];

        foreach ($policy->recipient_scopes ?? [] as $scopeValue) {
            $scope = VisibilityScope::tryFrom((string) $scopeValue);

            if ($scope === null) {
                continue;
            }

            foreach ($this->recipientsFor($scope, $document, $policy, $schoolId) as $recipient) {
                $recipients[] = $recipient;
            }
        }

        return $recipients;
    }

    /**
     * @return list<DocumentRecipientData>
     */
    private function recipientsFor(
        VisibilityScope $scope,
        AcademicDocument $document,
        PublicationPolicy $policy,
        string $schoolId,
    ): array {
        return match ($scope) {
            VisibilityScope::Director => $this->staffRecipients($schoolId, 'director', $scope),
            VisibilityScope::Secretary => $this->staffRecipients($schoolId, 'secretary', $scope),
            VisibilityScope::Teacher => $this->staffRecipients($schoolId, 'teacher', $scope),
            VisibilityScope::Inspector => $this->inspectorRecipients($schoolId, $policy),
            VisibilityScope::Parent => $this->parentRecipients($schoolId, $document),
            VisibilityScope::Student => $this->studentRecipients($schoolId, $document),
        };
    }

    /**
     * @return list<DocumentRecipientData>
     */
    private function staffRecipients(string $schoolId, string $role, VisibilityScope $scope): array
    {
        if (! Role::query()->where('name', $role)->where('guard_name', 'web')->exists()) {
            return [];
        }

        return User::query()
            ->where('school_id', $schoolId)
            ->role($role)
            ->orderBy('id')
            ->get()
            ->map(fn (User $user): DocumentRecipientData => new DocumentRecipientData(
                recipientType: 'staff',
                recipientId: $user->getKey(),
                recipientLabel: (string) $user->full_name,
                visibilityScope: $scope,
                userId: $user->getKey(),
            ))
            ->all();
    }

    /**
     * @return list<DocumentRecipientData>
     */
    private function inspectorRecipients(string $schoolId, PublicationPolicy $policy): array
    {
        $ids = $policy->settings['inspector_recipients'] ?? [];

        if (! is_array($ids)) {
            return [];
        }

        $ids = array_values(array_unique(array_filter(
            array_map('intval', $ids),
            static fn (int $id): bool => $id > 0,
        )));

        return User::query()
            ->where('school_id', $schoolId)
            ->whereKey($ids)
            ->orderBy('id')
            ->get()
            ->map(fn (User $user): DocumentRecipientData => new DocumentRecipientData(
                recipientType: 'staff',
                recipientId: $user->getKey(),
                recipientLabel: (string) $user->full_name,
                visibilityScope: VisibilityScope::Inspector,
                userId: $user->getKey(),
            ))
            ->all();
    }

    /**
     * @return list<DocumentRecipientData>
     */
    private function parentRecipients(string $schoolId, AcademicDocument $document): array
    {
        if ($document->student_id === null) {
            return [];
        }

        $guards = $this->guardian->guardiansForStudent($schoolId, $document->student_id);

        return array_map(
            static fn ($guardian): DocumentRecipientData => new DocumentRecipientData(
                recipientType: 'guardian',
                recipientId: $guardian->id,
                recipientLabel: $guardian->fullName,
                visibilityScope: VisibilityScope::Parent,
                userId: $guardian->userId,
                metadata: ['student_id' => $document->student_id],
            ),
            $guards,
        );
    }

    /**
     * @return list<DocumentRecipientData>
     */
    private function studentRecipients(string $schoolId, AcademicDocument $document): array
    {
        if ($document->student_id === null) {
            return [];
        }

        $student = $this->studentIdentity->student($schoolId, $document->student_id);

        if ($student === null) {
            return [];
        }

        return [
            new DocumentRecipientData(
                recipientType: 'student',
                recipientId: $student->id,
                recipientLabel: $student->fullName,
                visibilityScope: VisibilityScope::Student,
                userId: $student->userId,
                metadata: ['student_id' => $student->id],
            ),
        ];
    }
}
