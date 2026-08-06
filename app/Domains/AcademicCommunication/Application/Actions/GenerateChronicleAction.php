<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\Services\DocumentContentComposer;
use App\Domains\AcademicCommunication\Domain\Contracts\AcademicPoliciesContract;
use App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentOrigin;
use App\Domains\AcademicCommunication\Domain\Exceptions\MissingDocumentContextException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Models\User;

/**
 * Generates the school chronicle of an academic year (SPR-012). The chronicle
 * merges the validated class-level gradebook aggregate with the school's
 * official academic policies (grading scale, honor thresholds, …) supplied by
 * the Academic Policies engine.
 */
final class GenerateChronicleAction
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly DocumentContentComposer $composer,
        private readonly GradebookContract $gradebook,
        private readonly AcademicPoliciesContract $policies,
    ) {
    }

    public function execute(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        if ($data->yearId === null) {
            throw MissingDocumentContextException::missing('academic year');
        }

        $summary = $data->classId !== null
            ? $this->gradebook->classSummary($data->schoolId, $data->classId, $data->yearId, $data->termId ?? 0)
            : null;

        $policies = $this->policies->policies($data->schoolId, ['grading_scale', 'honor_threshold', 'assessment_rules']);

        $content = array_merge(
            $this->composer->schoolBlock($data->schoolId),
            $this->composer->frameBlock($data->schoolId, $data->yearId, $data->termId, $data->classId, $data->sectionId),
            [
                'summary' => $summary !== null
                    ? [
                        'class_average' => $summary->classAverage,
                        'pass_rate' => $summary->passRate,
                    ]
                    : null,
                'policies' => array_map(
                    static fn ($policy): array => [
                        'key' => $policy->key,
                        'label' => $policy->label,
                        'value' => $policy->value,
                    ],
                    $policies,
                ),
            ],
        );

        return $this->generateDocument->execute(
            $data,
            $actor,
            DocumentOrigin::SchoolChronicle,
            $content,
        );
    }
}
