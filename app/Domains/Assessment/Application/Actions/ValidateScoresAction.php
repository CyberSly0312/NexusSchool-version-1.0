<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Application\Actions;

use App\Domains\Assessment\Application\Services\ScoreValidationService;
use App\Domains\Assessment\Domain\Enums\ScoreValidationStatus;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use Illuminate\Support\Facades\DB;

/**
 * Recomputes validation status for a score collection (SPR-009).
 *
 * Validation only inspects the recorded score against the maximum score of the
 * assessment or its component; it never rewrites the score itself.
 */
final class ValidateScoresAction
{
    public function __construct(
        private readonly ScoreValidationService $validation,
    ) {
    }

    /**
     * @param  array{assessment_component_id?: int|string|null, from_score?: int, limit?: int}  $options
     *
     * @return array{validated: int, invalid: int}
     */
    public function execute(Assessment $assessment, array $options = []): array
    {
        $query = AssessmentScore::query()->where('assessment_id', $assessment->getKey());

        if (array_key_exists('assessment_component_id', $options) && $options['assessment_component_id'] !== null) {
            $query->where('assessment_component_id', $options['assessment_component_id']);
        } elseif (array_key_exists('assessment_component_id', $options)) {
            $query->whereNull('assessment_component_id');
        }

        if (array_key_exists('from_score', $options)) {
            $query->where('id', '>', (int) $options['from_score']);
        }

        if (array_key_exists('limit', $options)) {
            $query->limit((int) $options['limit']);
        }

        $validated = 0;
        $invalid = 0;

        $query->chunkById(200, function ($scores) use ($assessment, &$validated, &$invalid): void {
            /** @var AssessmentScore $score */
            foreach ($scores as $score) {
                $maximum = $this->maximumFor($assessment, $score);

                $status = $this->validation->validate($score->score, $maximum);

                if ($status === ScoreValidationStatus::Valid) {
                    $validated++;
                } else {
                    $invalid++;
                }

                if ($score->validation_status === $status->value) {
                    continue;
                }

                DB::table('assessment_scores')
                    ->where('id', $score->getKey())
                    ->update(['validation_status' => $status->value]);
            }
        });

        return ['validated' => $validated, 'invalid' => $invalid];
    }

    private function maximumFor(Assessment $assessment, AssessmentScore $score): float
    {
        if ($score->assessment_component_id === null) {
            return (float) $assessment->maximum_score;
        }

        $component = $assessment->components()->find($score->assessment_component_id);

        return $component !== null ? (float) $component->maximum_score : (float) $assessment->maximum_score;
    }
}
