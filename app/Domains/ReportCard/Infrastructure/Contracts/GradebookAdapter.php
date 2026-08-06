<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Infrastructure\Contracts;

use App\Domains\Gradebook\Application\Services\AcademicStandingService;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Domain\References\StandingContributionReference;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use App\Domains\ReportCard\Domain\Contracts\GradebookContract;
use App\Domains\ReportCard\Domain\References\GradebookEntryReference;
use App\Domains\ReportCard\Domain\References\GradebookStandingReference;
use Illuminate\Database\Eloquent\Collection;

/**
 * GradebookContract implementation (SPR-011).
 *
 * Projects the published (and archived) Gradebook models and the student-level
 * aggregates computed by the Gradebook engine onto the contract. Only published
 * gradebooks feed bulletins, and every figure is read verbatim — the Report
 * Card engine never recomputes an average or a GPA.
 */
final class GradebookAdapter implements GradebookContract
{
    private const READABLE_GRADEBOOK_STATUSES = ['published', 'archived'];

    public function __construct(
        private readonly AcademicStandingService $standing,
    ) {
    }

    public function studentGrades(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): array {
        $entries = $this->publishedEntries($schoolId, $studentId, $yearId, $termId);

        if ($entries['gradebooks']->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($entries['gradebooks'] as $gradebook) {
            $entry = $entries['entries']->get($gradebook->getKey());

            if ($entry === null) {
                continue;
            }

            $subject = $gradebook->subject;
            $coefficient = (float) $gradebook->coefficient;

            $result[] = new GradebookEntryReference(
                subjectId: $subject?->getKey() ?? $gradebook->academic_subject_id,
                subjectName: (string) ($subject?->name ?? 'Matière'),
                subjectCode: $subject?->code,
                average: (float) $entry->average,
                percentage: (float) $entry->percentage,
                coefficient: $coefficient,
                gradePoint: (float) $entry->grade_point,
                letterGrade: $entry->letter_grade,
                appreciation: $entry->appreciation,
                rank: $entry->rank,
                rankDenominator: $entry->rank_denominator,
            );
        }

        return $result;
    }

    public function studentStanding(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): ?GradebookStandingReference {
        $data = $this->publishedEntries($schoolId, $studentId, $yearId, $termId);

        $contributions = [];

        foreach ($data['gradebooks'] as $gradebook) {
            $entry = $data['entries']->get($gradebook->getKey());

            if ($entry === null) {
                continue;
            }

            $contributions[] = new StandingContributionReference(
                percentage: (float) $entry->percentage,
                gradePoint: (float) $entry->grade_point,
                coefficient: (float) $gradebook->coefficient,
            );
        }

        if ($contributions === []) {
            return null;
        }

        $passingPercentage = (float) config('nexus-gradebook.standing.passing_percentage', 50.0);

        return new GradebookStandingReference(
            gpa: $this->standing->gpa($contributions)?->gpa,
            cgpa: $this->standing->cgpa($contributions)?->gpa,
            overallPercentage: $this->standing->overallPercentage($contributions),
            academicStanding: $this->standing->standing($contributions)?->value,
            failedSubjects: count(array_filter(
                $contributions,
                static fn (StandingContributionReference $contribution): bool => $contribution->percentage < $passingPercentage,
            )),
        );
    }

    /**
     * @return array{gradebooks: Collection<int, Gradebook>, entries: Collection<int, GradeEntry>}
     */
    private function publishedEntries(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): array {
        $gradebooks = Gradebook::query()
            ->forSchool($schoolId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('academic_calendar_term_id', $termId)
            ->whereIn('status', self::READABLE_GRADEBOOK_STATUSES)
            ->with('subject')
            ->get();

        if ($gradebooks->isEmpty()) {
            return ['gradebooks' => $gradebooks, 'entries' => new Collection()];
        }

        $entries = GradeEntry::query()
            ->forSchool($schoolId)
            ->whereIn('gradebook_id', $gradebooks->pluck('id'))
            ->where('student_id', $studentId)
            ->where('status', GradeEntryStatus::Published->value)
            ->get()
            ->keyBy('gradebook_id');

        return ['gradebooks' => $gradebooks, 'entries' => $entries];
    }
}
