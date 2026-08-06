<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Domain\Contracts\ClassCouncilContract;
use App\Domains\AcademicCommunication\Domain\Contracts\StudentIdentityContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Exceptions\MissingDocumentContextException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;

/**
 * Generates the minutes (procès-verbal) of a class council (SPR-012).
 *
 * The engine consumes only *published* council decisions through the
 * ClassCouncilContract — confidential deliberations never leave the council
 * domain and can never appear in a rendered document.
 */
final class GenerateCouncilMinutesAction
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly DocumentContentComposer $composer,
        private readonly ClassCouncilContract $classCouncil,
        private readonly StudentIdentityContract $studentIdentity,
    ) {
    }

    public function execute(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        if ($data->councilSessionId === null) {
            throw MissingDocumentContextException::missing('council session');
        }

        $decisions = $this->classCouncil->publishedDecisions($data->schoolId, $data->councilSessionId);

        $studentIds = array_values(array_unique(array_filter(
            array_map(
                static fn ($decision): int|string|null => $decision->studentId,
                $decisions,
            ),
            static fn (int|string|null $id): bool => $id !== null,
        )));

        $students = $studentIds !== [] ? $this->studentIdentity->students($data->schoolId, $studentIds) : [];

        $studentsById = [];
        foreach ($students as $student) {
            $studentsById[(string) $student->id] = $student->fullName;
        }

        $content = array_merge(
            $this->composer->schoolBlock($data->schoolId),
            $this->composer->frameBlock($data->schoolId, $data->yearId, $data->termId, $data->classId, $data->sectionId),
            [
                'decisions' => array_map(
                    static fn ($decision): array => [
                        'id' => $decision->id,
                        'summary' => $decision->summary,
                        'category' => $decision->category,
                        'student' => isset($decision->studentId) ? ($studentsById[(string) $decision->studentId] ?? null) : null,
                        'recommendation' => $decision->recommendation,
                        'decision_date' => $decision->decisionDate,
                    ],
                    $decisions,
                ),
            ],
        );

        return $this->generateDocument->execute(
            $data,
            $actor,
            DocumentOrigin::CouncilMinutes,
            $content,
        );
    }
}
