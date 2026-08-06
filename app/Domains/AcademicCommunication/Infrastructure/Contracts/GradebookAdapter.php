<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Infrastructure\Contracts;

use App\Domains\AcademicCommunication\Domain\Contracts\GradebookContract;
use App\Domains\AcademicCommunication\Domain\References\ClassReference;
use App\Domains\AcademicCommunication\Domain\References\GradebookEntryReference;
use App\Domains\AcademicCommunication\Domain\References\GradebookSummaryReference;
use App\Domains\AcademicCommunication\Domain\References\SubjectReference;
use App\Domains\AcademicCommunication\Domain\References\TermReference;
use App\Domains\Gradebook\Domain\Enums\GradeEntryStatus;
use App\Domains\Gradebook\Infrastructure\Models\Gradebook;
use App\Domains\Gradebook\Infrastructure\Models\GradeEntry;
use Illuminate\Database\Eloquent\Collection;

/**
 * GradebookContract implementation (SPR-012).
 *
 * The Gradebook engine (SPR-010) supplies validated term grades; this adapter
 * projects its models onto the contract so the Communication engine never sees
 * a Gradebook model directly. Only published (and archived) gradebooks feed
 * report cards and class summaries.
 */
final class GradebookAdapter implements GradebookContract
{
    private const READABLE_GRADEBOOK_STATUSES = ['published', 'archived'];

    public function studentGrades(
        string $schoolId,
        int|string $studentId,
        int|string $yearId,
        int|string $termId,
    ): array {
        $gradebooks = $this->periodGradebooks($schoolId, $yearId, $termId);

        if ($gradebooks->isEmpty()) {
            return [];
        }

        $entries = GradeEntry::query()
            ->forSchool($schoolId)
            ->whereIn('gradebook_id', $gradebooks->pluck('id'))
            ->where('student_id', $studentId)
            ->where('status', GradeEntryStatus::Published->value)
            ->get()
            ->keyBy('gradebook_id');

        $result = [];

        foreach ($gradebooks as $gradebook) {
            $entry = $entries->get($gradebook->getKey());

            if ($entry === null) {
                continue;
            }

            $subject = $gradebook->subject;
            $coefficient = (float) $gradebook->coefficient;

            $result[] = new GradebookEntryReference(
                subject: new SubjectReference(
                    id: $subject?->getKey() ?? $gradebook->academic_subject_id,
                    name: (string) ($subject?->name ?? 'Matière'),
                    code: $subject?->code,
                    coefficient: $coefficient,
                ),
                average: (float) $entry->average,
                coefficient: $coefficient,
                weightedAverage: round((float) $entry->average * $coefficient, 2),
                letterGrade: $entry->letter_grade,
                appreciation: $entry->appreciation,
                rank: $entry->rank,
                assessmentBreakdown: [],
            );
        }

        return $result;
    }

    public function classSummary(
        string $schoolId,
        int|string $classId,
        int|string $yearId,
        int|string $termId,
    ): ?GradebookSummaryReference {
        $gradebooks = Gradebook::query()
            ->forSchool($schoolId)
            ->where('school_class_id', $classId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('academic_calendar_term_id', $termId)
            ->whereIn('status', self::READABLE_GRADEBOOK_STATUSES)
            ->with(['subject', 'schoolClass', 'term'])
            ->get();

        if ($gradebooks->isEmpty()) {
            return null;
        }

        $entries = GradeEntry::query()
            ->forSchool($schoolId)
            ->whereIn('gradebook_id', $gradebooks->pluck('id'))
            ->where('status', GradeEntryStatus::Published->value)
            ->whereNotNull('percentage')
            ->get();

        if ($entries->isEmpty()) {
            return null;
        }

        $subjects = $gradebooks->keyBy('id');

        $classAverage = round((float) $entries->avg('average'), 2);
        $passRate = round(
            $entries->filter(fn (GradeEntry $entry): bool => (float) $entry->percentage >= 50.0)->count() / max(1, $entries->count()) * 100,
            2,
        );

        $maxRows = (int) config('nexus-gradebook.ranking.max_report_rows', 50);

        $entryReferences = $entries
            ->sortBy([['gradebook_id', 'asc'], ['rank', 'asc']])
            ->take($maxRows)
            ->map(function (GradeEntry $entry) use ($subjects): GradebookEntryReference {
                $gradebook = $subjects->get($entry->gradebook_id);
                $subject = $gradebook?->subject;
                $coefficient = (float) ($gradebook?->coefficient ?? 1.0);

                return new GradebookEntryReference(
                    subject: new SubjectReference(
                        id: $subject?->getKey() ?? $entry->gradebook_id,
                        name: (string) ($subject?->name ?? 'Matière'),
                        code: $subject?->code,
                        coefficient: $coefficient,
                    ),
                    average: (float) $entry->average,
                    coefficient: $coefficient,
                    weightedAverage: round((float) $entry->average * $coefficient, 2),
                    letterGrade: $entry->letter_grade,
                    appreciation: $entry->appreciation,
                    rank: $entry->rank,
                    assessmentBreakdown: [],
                );
            })
            ->values()
            ->all();

        $schoolClass = $gradebooks->first()?->schoolClass;
        $term = $gradebooks->first()?->term;

        return new GradebookSummaryReference(
            class: new ClassReference(
                id: $classId,
                name: (string) ($schoolClass?->name ?? 'Classe'),
                code: $schoolClass?->code,
            ),
            term: new TermReference(
                id: $termId,
                name: (string) ($term?->name ?? 'Période'),
                code: $term?->code,
            ),
            classAverage: $classAverage,
            passRate: $passRate,
            entries: $entryReferences,
        );
    }

    /**
     * @return Collection<int, Gradebook>
     */
    private function periodGradebooks(string $schoolId, int|string $yearId, int|string $termId): Collection
    {
        return Gradebook::query()
            ->forSchool($schoolId)
            ->where('academic_calendar_year_id', $yearId)
            ->where('academic_calendar_term_id', $termId)
            ->whereIn('status', self::READABLE_GRADEBOOK_STATUSES)
            ->with('subject')
            ->get();
    }
}
