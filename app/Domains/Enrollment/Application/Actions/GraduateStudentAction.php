<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Application\Actions;

use App\Domains\Enrollment\Application\DTOs\GraduationData;
use App\Domains\Enrollment\Application\Services\EnrollmentAcademicResolver;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Events\StudentGraduated;
use App\Domains\Enrollment\Domain\Exceptions\EnrollmentStateException;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class GraduateStudentAction
{
    public function __construct(
        private readonly EnrollmentAcademicResolver $resolver,
    ) {
    }

    public function execute(Enrollment $enrollment, GraduationData $data, ?User $actor = null): Enrollment
    {
        if ($enrollment->isImmutable() || ! $enrollment->isActive()) {
            throw EnrollmentStateException::transition($enrollment->status, 'graduated');
        }

        $this->resolver->assertEducationLevelInSchool($enrollment->school_id, $data->educationLevelId);

        return DB::transaction(function () use ($enrollment, $data, $actor): Enrollment {
            $graduation = EnrollmentGraduation::create([
                'school_id' => $enrollment->school_id,
                'enrollment_id' => $enrollment->getKey(),
                'student_id' => $enrollment->student_id,
                'education_level_id' => $data->educationLevelId,
                'decision' => $data->decision->value,
                'graduated_on' => $data->graduatedOn,
                'certificate_reference' => $data->certificateReference,
                'notes' => $data->notes,
            ]);

            $enrollment->status = EnrollmentStatus::Graduated;
            $enrollment->exited_at = $data->graduatedOn;
            $enrollment->save();

            StudentGraduated::dispatch($enrollment, $graduation, $actor);

            return $enrollment;
        });
    }
}
