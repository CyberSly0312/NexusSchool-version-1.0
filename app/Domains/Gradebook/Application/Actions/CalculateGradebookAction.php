<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Application\Actions;

use App\Domains\Gradebook\Application\Services\GradeCalculationService;
use App\Domains\Gradebook\Application\Services\GradeRankingService;
use App\Domains\Gradebook\Domain\Contracts\AssessmentContract;
use App\Domains\Gradebook\Domain\Contracts\EnrollmentContract;
use App\Domains\Gradebook\Domain\Enums\GradebookStatus;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\Enums\LetterGrade;
use App\Domains\Gradebook\Domain\Events\GradebookCalculated;
use App\Domains\Gradebook\Domain\Events\RankingsCalculated;
use App\Domains\Gradebook\Domain\Exceptions\GradebookLockedException;
use App\Domains\Gradebook\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domains\Gradebook\Domain\References\GradeEntryReference;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Computes the term grade of every enrolled student of a gradebook (SPR-010).
 *
 * The average is the weighted mean of the published assessment percentages.
 * Entries are ranked using standard competition ranking. Students without any
 * published contribution receive no entry: an absence of data is not a zero.
 * Recalculating a draft/calculated gradebook refreshes the entries and drops
 * entries of students no longer in scope.
 */
final class CalculateGradebookAction
{
    public function __construct(
        private readonly AssessmentContract $assessment,
        private readonly EnrollmentContract $enrollment,
        private readonly GradeCalculationService $calculator,
        private readonly GradeRankingService $ranking,
    ) {
    }

    public function execute(Gradebook $gradebook, ?User $actor = null): Gradebook
    {
        return DB::transaction(function () use ($gradebook, $actor): Gradebook {
            $this->assertCalculable($gradebook);

            $scale = (int) config('nexus-gradebook.grade_system.scale', 20);
            $decimalPlaces = (int) config('nexus-gradebook.grade_system.decimal_places', 2);

            $enrollmentByStudent = [];
            foreach ($this->enrollment->enrollmentsOfClass(
                $gradebook->school_id,
                $gradebook->school_class_id,
                $gradebook->academic_calendar_year_id,
                $gradebook->class_section_id,
            ) as $enrollment) {
                $enrollmentByStudent[(string) $enrollment->studentId] = $enrollment->id;
            }

            $students = $this->enrollment->studentsOfClass(
                $gradebook->school_id,
                $gradebook->school_class_id,
                $gradebook->academic_calendar_year_id,
                $gradebook->class_section_id,
            );

            $entries = [];
            $percentages = [];

            foreach ($students as $student) {
                $contributions = $this->assessment->studentAssessmentContributions(
                    $gradebook->school_id,
                    $student->id,
                    $gradebook->academic_calendar_year_id,
                    $gradebook->academic_calendar_term_id,
                    $gradebook->academic_subject_id,
                    $gradebook->school_class_id,
                    $gradebook->class_section_id,
                );

                $percentage = $this->calculator->calculateTermPercentage($contributions);

                if ($percentage === null) {
                    continue;
                }

                $percentages[(string) $student->id] = $percentage;

                $entries[] = new GradeEntryReference(
                    student: $student,
                    percentage: $percentage,
                    average: round(($percentage / 100.0) * $scale, $decimalPlaces),
                    letterGrade: LetterGrade::fromPercentage($percentage),
                );
            }

            $ranks = $this->ranking->rank($percentages);

            $autoValidate = (bool) config('nexus-gradebook.validation.auto_validate_on_calculate', true);
            $entryStatus = $autoValidate ? GradeEntryStatus::Validated : GradeEntryStatus::Draft;

            $this->persistEntries($gradebook, $entries, $ranks, $enrollmentByStudent, $entryStatus, $actor);

            $gradebook->status = GradebookStatus::Calculated;
            $gradebook->calculated_at = now();
            $gradebook->locked_at = null;
            $gradebook->save();

            GradebookCalculated::dispatch($gradebook, count($entries), $actor);
            RankingsCalculated::dispatch($gradebook, $actor);

            return $gradebook;
        });
    }

    /**
     * @param  list<GradeEntryReference>  $entries
     * @param  array<string, \App\Domains\Gradebook\Domain\References\RankingReference>  $ranks
     * @param  array<string, int|string>  $enrollmentByStudent
     */
    private function persistEntries(
        Gradebook $gradebook,
        array $entries,
        array $ranks,
        array $enrollmentByStudent,
        GradeEntryStatus $status,
        ?User $actor,
    ): void {
        $existing = GradeEntry::query()
            ->where('gradebook_id', $gradebook->getKey())
            ->get()
            ->keyBy('enrollment_id');

        $seen = [];

        foreach ($entries as $entry) {
            $studentId = (string) $entry->student->id;
            $enrollmentId = $enrollmentByStudent[$studentId] ?? null;
            $rank = $ranks[$studentId] ?? null;

            GradeEntry::query()->updateOrCreate(
                [
                    'gradebook_id' => $gradebook->getKey(),
                    'enrollment_id' => $enrollmentId,
                ],
                [
                    'school_id' => $gradebook->school_id,
                    'student_id' => $entry->student->id,
                    'percentage' => $entry->percentage,
                    'average' => $entry->average,
                    'letter_grade' => $entry->letterGrade->value,
                    'grade_point' => $entry->gradePoint(),
                    'appreciation' => $entry->appreciation(),
                    'rank' => $rank?->rank,
                    'rank_denominator' => $rank?->denominator,
                    'status' => $status,
                    'graded_by' => $actor?->getKey(),
                    'calculated_at' => now(),
                ],
            );

            $seen[] = (string) $entry->student->id;
        }

        $existing->each(function (GradeEntry $entry) use ($seen): void {
            if (! in_array((string) $entry->student_id, $seen, true)) {
                $entry->delete();
            }
        });
    }

    private function assertCalculable(Gradebook $gradebook): void
    {
        if ($gradebook->status === GradebookStatus::Locked) {
            throw GradebookLockedException::for($gradebook->getKey());
        }

        if (! in_array($gradebook->status, [GradebookStatus::Draft, GradebookStatus::Calculated], true)) {
            throw InvalidStatusTransitionException::from($gradebook->status, GradebookStatus::Calculated);
        }
    }
}
