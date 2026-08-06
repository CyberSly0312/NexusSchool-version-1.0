<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\DTOs\UpdateAssessmentData;
use App\Domains\Assessment\Domain\Exceptions\WeightTotalExceededException;
use App\Domains\Assessment\Domain\ValueObjects\MaximumScore;
use App\Domains\Assessment\Domain\ValueObjects\PassingScore;
use App\Domains\Assessment\Domain\ValueObjects\Weight;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Edits the definition of a draft or scheduled assessment (SPR-009). Published,
 * completed and archived assessments are definition-immutable.
 */
final class UpdateAssessmentAction
{
    public function execute(UpdateAssessmentData $data, ?User $actor = null): Assessment
    {
        return DB::transaction(function () use ($data, $actor): Assessment {
            $assessment = Assessment::query()->findOrFail($data->assessmentId);

            $assessment->assertDefinitionMutable();

            $maximum = MaximumScore::fromFloat($data->maximumScore);
            $passing = $data->passingScore !== null
                ? PassingScore::fromFloat($data->passingScore, $maximum->value)
                : null;
            $weight = $data->weight !== null ? Weight::fromPercent($data->weight) : null;

            $this->assertWeightPolicy($assessment, $weight);

            $assessment->update([
                'title' => $data->title,
                'description' => $data->description,
                'maximum_score' => $maximum->value,
                'passing_score' => $passing?->value,
                'weight' => $weight?->value,
                'scheduled_at' => $data->scheduledAt,
                'notes' => $data->notes,
            ]);

            return $assessment->refresh();
        });
    }

    private function assertWeightPolicy(Assessment $assessment, ?Weight $weight): void
    {
        if ($weight === null) {
            return;
        }

        $enforced = (bool) config('nexus-assessment.weights.enforce_total', true);

        if (! $enforced) {
            return;
        }

        $ceiling = (float) config('nexus-assessment.weights.max_total', 100.0);
        $current = Assessment::query()
            ->where('school_id', $assessment->school_id)
            ->where('academic_subject_id', $assessment->academic_subject_id)
            ->where('academic_calendar_term_id', $assessment->academic_calendar_term_id)
            ->where('id', '!=', $assessment->getKey())
            ->where('status', '!=', 'archived')
            ->whereNotNull('weight')
            ->sum('weight');

        $total = $current + $weight->value;

        if ($total > $ceiling) {
            throw WeightTotalExceededException::forTotal($total, $ceiling);
        }
    }
}
