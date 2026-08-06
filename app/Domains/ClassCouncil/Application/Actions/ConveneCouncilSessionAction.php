<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Actions;

use App\Domains\ClassCouncil\Application\Services\AutomaticDecisionService;
use App\Domains\ClassCouncil\Domain\Contracts\EnrollmentContract;
use App\Domains\ClassCouncil\Domain\Contracts\GradebookContract;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionConvened;
use App\Domains\ClassCouncil\Domain\References\CouncilReviewReference;
use App\Domains\ClassCouncil\Domain\References\EnrollmentReference;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Convenes a draft council session (SPR-013).
 *
 * Transitioning to Convened builds the review list of every active student of
 * the session's class, each paired with its published academic standing and the
 * automatic decision the council may confirm or override. The review list is
 * returned to the caller; the persisted academic history is only the decisions
 * and deliberations the council records.
 */
final class ConveneCouncilSessionAction
{
    public function __construct(
        private readonly EnrollmentContract $enrollment,
        private readonly GradebookContract $gradebook,
        private readonly AutomaticDecisionService $automatic,
    ) {
    }

    /**
     * @return list<CouncilReviewReference>
     */
    public function execute(CouncilSession $session, ?User $actor = null): array
    {
        return DB::transaction(function () use ($session, $actor): array {
            $session->assertCanTransitionTo(CouncilSessionStatus::Convened);

            $reviews = $this->buildReviewList($session);

            $session->status = CouncilSessionStatus::Convened;
            $session->held_at = now();
            $session->save();

            CouncilSessionConvened::dispatch($session, $reviews, $actor);

            return $reviews;
        });
    }

    /**
     * @return list<CouncilReviewReference>
     */
    private function buildReviewList(CouncilSession $session): array
    {
        $enrollments = $this->enrollment->activeEnrollments(
            $session->school_id,
            $session->academic_calendar_year_id,
            $session->school_class_id,
            $session->class_section_id,
        );

        return array_map(function (EnrollmentReference $enrollment) use ($session): CouncilReviewReference {
            $standing = $this->gradebook->studentStanding(
                $session->school_id,
                $enrollment->student->id,
                $session->academic_calendar_year_id,
                $session->academic_calendar_term_id,
            );

            return new CouncilReviewReference(
                enrollmentId: $enrollment->id,
                student: $enrollment->student,
                standing: $standing,
                automatic: $this->automatic->propose($standing),
            );
        }, $enrollments);
    }
}
