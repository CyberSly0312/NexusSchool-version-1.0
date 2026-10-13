<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application;

use App\Domains\AcademicCommunication\Application\Actions\ApproveDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\ArchiveDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateChronicleAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateCouncilMinutesAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateParentSummaryAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateReportCardAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateStudentSummaryAction;
use App\Domains\AcademicCommunication\Application\Actions\GenerateTeacherSummaryAction;
use App\Domains\AcademicCommunication\Application\Actions\PublishDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\RegenerateDocumentAction;
use App\Domains\AcademicCommunication\Application\Actions\ReviewDocumentAction;
use App\Domains\AcademicCommunication\Application\DTOs\ApproveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\ArchiveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\GenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\RegenerateDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\ReviewDocumentData;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocumentVersion;
use App\Models\User;

/**
 * Application facade of the Academic Communication Engine (SPR-012). Delegates
 * to the action objects so feature calls stay single-line and actions stay
 * individually testable.
 */
final class AcademicCommunicationService
{
    public function __construct(
        private readonly GenerateDocumentAction $generateDocument,
        private readonly GenerateReportCardAction $generateReportCard,
        private readonly GenerateCouncilMinutesAction $generateCouncilMinutes,
        private readonly GenerateParentSummaryAction $generateParentSummary,
        private readonly GenerateTeacherSummaryAction $generateTeacherSummary,
        private readonly GenerateStudentSummaryAction $generateStudentSummary,
        private readonly GenerateChronicleAction $generateChronicle,
        private readonly ReviewDocumentAction $reviewDocument,
        private readonly ApproveDocumentAction $approveDocument,
        private readonly PublishDocumentAction $publishDocument,
        private readonly ArchiveDocumentAction $archiveDocument,
        private readonly RegenerateDocumentAction $regenerateDocument,
    ) {
    }

    public function generateDocument(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateDocument->execute($data, $actor);
    }

    public function generateReportCard(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateReportCard->execute($data, $actor);
    }

    public function generateCouncilMinutes(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateCouncilMinutes->execute($data, $actor);
    }

    public function generateParentSummary(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateParentSummary->execute($data, $actor);
    }

    public function generateTeacherSummary(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateTeacherSummary->execute($data, $actor);
    }

    public function generateStudentSummary(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateStudentSummary->execute($data, $actor);
    }

    public function generateChronicle(GenerateDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->generateChronicle->execute($data, $actor);
    }

    public function review(ReviewDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->reviewDocument->execute($data, $actor);
    }

    public function approve(ApproveDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->approveDocument->execute($data, $actor);
    }

    public function publish(PublishDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->publishDocument->execute($data, $actor);
    }

    public function archive(ArchiveDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return $this->archiveDocument->execute($data, $actor);
    }

    public function regenerate(RegenerateDocumentData $data, ?User $actor = null): AcademicDocumentVersion
    {
        return $this->regenerateDocument->execute($data, $actor);
    }
}
