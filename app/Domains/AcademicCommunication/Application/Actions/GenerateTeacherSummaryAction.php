<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Exceptions\MissingDocumentContextException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;

/**
 * Generates the teacher summary of a class period (SPR-012). The summary is
 * composed from the class-level Gradebook aggregate and delivered to the
 * teaching staff of the school through the policy scopes.
 */
final class GenerateTeacherSummaryAction
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly DocumentContentComposer $composer,
        private readonly GradebookContract $gradebook,
    ) {
    }

    public function execute(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        if ($data->classId === null || $data->yearId === null || $data->termId === null) {
            throw MissingDocumentContextException::missing('class / academic period');
        }

        $summary = $this->gradebook->classSummary(
            $data->schoolId,
            $data->classId,
            $data->yearId,
            $data->termId,
        );

        $content = array_merge(
            $this->composer->schoolBlock($data->schoolId),
            $this->composer->frameBlock($data->schoolId, $data->yearId, $data->termId, $data->classId, $data->sectionId),
            [
                'summary' => $summary !== null
                    ? [
                        'class_average' => $summary->classAverage,
                        'pass_rate' => $summary->passRate,
                        'entries' => array_map(
                            static fn ($entry): array => [
                                'subject' => $entry->subject->name,
                                'average' => $entry->average,
                                'rank' => $entry->rank,
                                'appreciation' => $entry->appreciation,
                            ],
                            $summary->entries,
                        ),
                    ]
                    : null,
            ],
        );

        return $this->generateDocument->execute(
            $data,
            $actor,
            DocumentOrigin::TeacherSummary,
            $content,
        );
    }
}
