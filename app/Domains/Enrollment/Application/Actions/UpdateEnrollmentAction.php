<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Application\Services\EnrollmentAcademicResolver;
use App\Domains\Enrollment\Domain\Events\EnrollmentUpdated;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Updates the mutable metadata of an ACTIVE enrollment (section reassignment,
 * notes, enrolment date). Identity and class changes are forbidden here and
 * must go through the transfer / promotion flows so history is preserved.
 */
final class UpdateEnrollmentAction
{
    public function __construct(
        private readonly EnrollmentAcademicResolver $resolver,
    ) {
    }

    public function execute(Enrollment $enrollment, UpdateEnrollmentData $data, ?User $actor = null): Enrollment
    {
        if ($enrollment->isImmutable()) {
            throw EnrollmentStateException::immutable($enrollment->getKey());
        }

        $this->resolver->assertSectionBelongsToEnrollmentClass($enrollment, $data->classSectionId);

        return DB::transaction(function () use ($enrollment, $data, $actor): Enrollment {
            $enrollment->class_section_id = $data->classSectionId ?? $enrollment->class_section_id;
            $enrollment->notes = $data->notes;
            $enrollment->enrolled_at = $data->enrolledAt ?? $enrollment->enrolled_at?->toDateString();
            $enrollment->save();

            EnrollmentUpdated::dispatch($enrollment, $actor);

            return $enrollment;
        });
    }
}
