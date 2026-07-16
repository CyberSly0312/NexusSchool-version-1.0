<?php

namespace App\Domains\Gradebook\Services;

use App\Domains\Gradebook\DTOs\AssessmentResultData;
use App\Domains\Gradebook\Enums\AssessmentResultStatus;
use App\Domains\Gradebook\Enums\LetterGrade;
use App\Domains\Gradebook\Events\AssessmentResultRecorded;
use App\Domains\Gradebook\Events\AssessmentResultVerified;
use App\Domains\Gradebook\Events\AssessmentResultsPublished;
use App\Domains\Gradebook\Events\GradebookCalculated;
use App\Domains\Gradebook\Events\RankingsCalculated;
use App\Domains\Gradebook\Rules\AssessmentResultRules;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\StudentEnrollment;
use App\Models\SubjectCoefficient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradebookService
{
    public function __construct(
        protected AssessmentResult $model = new AssessmentResult,
    ) {}

    public function record(AssessmentResultData $data): AssessmentResult
    {
        $validated = AssessmentResultRules::validate($data->toArray(), 'create');

        $this->validateScoreAgainstMax($validated['raw_score'], $validated['maximum_score']);
        $this->validateActiveEnrollment($validated['student_id'], $validated['academic_year_id']);
        $this->validateNoDuplicateResult($validated['student_id'], $validated['assessment_id']);

        if ($validated['adjusted_score'] !== null) {
            $this->validateScoreAgainstMax($validated['adjusted_score'], $validated['maximum_score']);
        }

        $percentage = $validated['is_absent']
            ? 0
            : round(($validated['raw_score'] / max($validated['maximum_score'], 1)) * 100, 2);

        $letterGrade = $validated['is_absent'] ? null : LetterGrade::fromPercentage($percentage)->value;
        $gradePoint = $validated['is_absent'] ? null : LetterGrade::gradePointFromPercentage($percentage);

        $coefficientValue = $this->resolveCoefficient(
            $validated['school_id'],
            $validated['subject_id'],
            $validated['student_id'],
        );

        $weightedScore = $validated['is_absent']
            ? null
            : round(($validated['raw_score']) * $coefficientValue, 4);

        $validated['percentage'] = $percentage;
        $validated['letter_grade'] = $letterGrade;
        $validated['grade_point'] = $gradePoint;
        $validated['coefficient'] = $coefficientValue;
        $validated['weighted_score'] = $weightedScore;

        return DB::transaction(function () use ($validated) {
            $result = $this->model->create($validated);

            AssessmentResultRecorded::dispatch($result);

            return $result;
        });
    }

    public function update(int $id, array $data): AssessmentResult
    {
        $result = $this->findById($id);

        if ($result->isReadOnly()) {
            throw ValidationException::withMessages([
                'status' => 'Published or locked results cannot be modified.',
            ]);
        }

        $validated = AssessmentResultRules::validate($data, 'update', $id);

        if (isset($validated['raw_score'])) {
            $maxScore = $validated['maximum_score'] ?? $result->maximum_score;
            $this->validateScoreAgainstMax($validated['raw_score'], $maxScore);
        }

        if (isset($validated['adjusted_score']) && $validated['adjusted_score'] !== null) {
            $maxScore = $validated['maximum_score'] ?? $result->maximum_score;
            $this->validateScoreAgainstMax($validated['adjusted_score'], $maxScore);
        }

        $rawScore = $validated['raw_score'] ?? $result->raw_score;
        $maxScore = $validated['maximum_score'] ?? $result->maximum_score;
        $isAbsent = $validated['is_absent'] ?? $result->is_absent;

        if (!$isAbsent) {
            $validated['percentage'] = round(($rawScore / max($maxScore, 1)) * 100, 2);
            $validated['letter_grade'] = LetterGrade::fromPercentage($validated['percentage'])->value;
            $validated['grade_point'] = LetterGrade::gradePointFromPercentage($validated['percentage']);
        }

        $result->update($validated);

        return $result->refresh();
    }

    public function delete(int $id): bool
    {
        $result = $this->findById($id);

        if (!$result->status->isDeletable()) {
            throw ValidationException::withMessages([
                'status' => 'Only draft or entered results can be deleted.',
            ]);
        }

        $result->delete();

        return true;
    }

    public function verify(int $id): AssessmentResult
    {
        $result = $this->findById($id);

        $this->validateTransition($result, AssessmentResultStatus::Verified);

        $result->update(['status' => AssessmentResultStatus::Verified]);

        AssessmentResultVerified::dispatch($result->fresh());

        return $result->refresh();
    }

    public function publish(int $assessmentId): int
    {
        $results = $this->model
            ->where('assessment_id', $assessmentId)
            ->whereIn('status', [AssessmentResultStatus::Verified->value, AssessmentResultStatus::Published->value])
            ->get();

        if ($results->isEmpty()) {
            throw ValidationException::withMessages([
                'assessment_id' => 'No verified results found to publish for this assessment.',
            ]);
        }

        $ids = $results->pluck('id')->toArray();

        DB::transaction(function () use ($ids) {
            $this->model->whereIn('id', $ids)->update([
                'status' => AssessmentResultStatus::Published,
                'is_published' => true,
            ]);
        });

        AssessmentResultsPublished::dispatch($ids, $assessmentId);

        return count($ids);
    }

    public function lock(int $id): AssessmentResult
    {
        $result = $this->findById($id);

        if ($result->isLocked()) {
            throw ValidationException::withMessages([
                'status' => 'Result is already locked.',
            ]);
        }

        $result->update([
            'status' => AssessmentResultStatus::Locked,
            'is_locked' => true,
        ]);

        return $result->refresh();
    }

    public function calculateGradebook(int $studentId, int $academicTermId, int $academicYearId): array
    {
        $results = $this->model
            ->where('student_id', $studentId)
            ->where('academic_term_id', $academicTermId)
            ->where('academic_year_id', $academicYearId)
            ->where('is_absent', false)
            ->whereIn('status', [
                AssessmentResultStatus::Verified->value,
                AssessmentResultStatus::Published->value,
                AssessmentResultStatus::Locked->value,
            ])
            ->get();

        if ($results->isEmpty()) {
            return [
                'term_average' => null,
                'weighted_average' => null,
                'total_weight' => 0,
                'result_count' => 0,
            ];
        }

        $totalWeightedScore = $results->sum('weighted_score');
        $totalCoefficient = $results->sum('coefficient');
        $termAverage = $totalCoefficient > 0
            ? round($totalWeightedScore / $totalCoefficient, 2)
            : 0;

        $totalWeight = $results->sum(function ($r) {
            return $r->assessment->weight ?? 1;
        });
        $weightedSum = $results->sum(function ($r) {
            return ($r->percentage ?? 0) * ($r->assessment->weight ?? 1);
        });
        $weightedAverage = $totalWeight > 0
            ? round($weightedSum / $totalWeight, 2)
            : null;

        GradebookCalculated::dispatch(
            $studentId,
            $academicTermId,
            $academicYearId,
            $termAverage,
            $weightedAverage,
        );

        return [
            'term_average' => $termAverage,
            'weighted_average' => $weightedAverage,
            'total_weight' => $totalWeight,
            'result_count' => $results->count(),
        ];
    }

    public function calculateRankings(int $assessmentId): Collection
    {
        $results = $this->model
            ->where('assessment_id', $assessmentId)
            ->where('is_absent', false)
            ->whereIn('status', [
                AssessmentResultStatus::Entered->value,
                AssessmentResultStatus::Verified->value,
                AssessmentResultStatus::Published->value,
            ])
            ->orderByDesc('percentage')
            ->get(['id', 'student_id', 'percentage']);

        if ($results->isEmpty()) {
            return collect();
        }

        DB::transaction(function () use ($results) {
            $rank = 1;
            $prevPercentage = null;

            foreach ($results as $result) {
                if ($prevPercentage !== null && $result->percentage < $prevPercentage) {
                    $rank++;
                }

                $this->model->where('id', $result->id)->update(['rank_in_assessment' => $rank]);
                $prevPercentage = $result->percentage;
            }
        });

        RankingsCalculated::dispatch($assessmentId, $results->count());

        return $results;
    }

    public function findById(int $id): AssessmentResult
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['student', 'assessment', 'subject', 'academicTerm']);

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('student_number', 'like', "%{$search}%");
                });
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['assessment_id'])) {
            $query->where('assessment_id', $filters['assessment_id']);
        }

        if (isset($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (isset($filters['academic_term_id'])) {
            $query->where('academic_term_id', $filters['academic_term_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function bulkRecord(array $resultsData): Collection
    {
        $created = collect();

        DB::transaction(function () use ($resultsData, &$created) {
            foreach ($resultsData as $data) {
                $dto = AssessmentResultData::fromArray($data);
                $created->push($this->record($dto));
            }
        });

        return $created;
    }

    public function getSubjectAverage(int $studentId, int $subjectId, int $academicTermId, int $academicYearId): ?float
    {
        $results = $this->model
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('academic_term_id', $academicTermId)
            ->where('academic_year_id', $academicYearId)
            ->where('is_absent', false)
            ->whereIn('status', [
                AssessmentResultStatus::Verified->value,
                AssessmentResultStatus::Published->value,
            ])
            ->get();

        if ($results->isEmpty()) {
            return null;
        }

        $totalWeighted = $results->sum(function ($r) {
            return $r->percentage * ($r->assessment->weight ?? 1);
        });
        $totalWeight = $results->sum(function ($r) {
            return $r->assessment->weight ?? 1;
        });

        return $totalWeight > 0 ? round($totalWeighted / $totalWeight, 2) : null;
    }

    private function validateScoreAgainstMax(float $score, float $maximumScore): void
    {
        if ($score < 0) {
            throw ValidationException::withMessages([
                'raw_score' => 'Score cannot be negative.',
            ]);
        }

        if ($score > $maximumScore) {
            throw ValidationException::withMessages([
                'raw_score' => "Raw score ({$score}) cannot exceed maximum score ({$maximumScore}).",
            ]);
        }
    }

    private function validateActiveEnrollment(int $studentId, int $academicYearId): void
    {
        $enrollment = StudentEnrollment::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->active()
            ->first();

        if (!$enrollment) {
            throw ValidationException::withMessages([
                'student_id' => 'Student does not have an active enrollment for this academic year.',
            ]);
        }
    }

    private function validateNoDuplicateResult(int $studentId, int $assessmentId): void
    {
        $exists = $this->model
            ->where('student_id', $studentId)
            ->where('assessment_id', $assessmentId)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'assessment_id' => 'A result already exists for this student and assessment.',
            ]);
        }
    }

    private function validateTransition(AssessmentResult $result, AssessmentResultStatus $target): void
    {
        if (!$result->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from '{$result->status->label()}' to '{$target->label()}'.",
            ]);
        }
    }

    private function resolveCoefficient(string $schoolId, int $subjectId, int $studentId): float
    {
        $coefficient = SubjectCoefficient::where('school_id', $schoolId)
            ->where('subject_id', $subjectId)
            ->active()
            ->effectiveBetween(now()->format('Y-m-d'))
            ->first();

        return $coefficient ? (float) $coefficient->coefficient : 1.00;
    }
}
