<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\Actions;

use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\ClassCouncil\Domain\Contracts\RecordsLifecycleContract;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Domain\Events\CouncilSessionCreated;
use App\Domains\ClassCouncil\Domain\Exceptions\ClassCouncilException;
use App\Domains\ClassCouncil\Infrastructure\Models\CouncilSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Creates a draft class council session (SPR-013). The academic frame is
 * validated through the domain contract; a session may be convened again for
 * the same class and period, so no scope uniqueness is enforced — the latest
 * published session feeds the downstream engines.
 */
final class CreateCouncilSessionAction
{
    public function __construct(
        private readonly AcademicFrameworkContract $academic,
        private readonly RecordsLifecycleContract $lifecycle,
    ) {
    }

    public function execute(CreateCouncilSessionData $data, ?User $actor = null): CouncilSession
    {
        return DB::transaction(function () use ($data, $actor): CouncilSession {
            $this->assertAcademicContext($data);

            $session = CouncilSession::create([
                'school_id' => $data->schoolId,
                'academic_calendar_year_id' => $data->yearId,
                'academic_calendar_term_id' => $data->termId,
                'school_class_id' => $data->classId,
                'class_section_id' => $data->sectionId,
                'reference' => 'CC-'.Str::upper(Str::random(8)),
                'status' => CouncilSessionStatus::Draft,
                'created_by' => $actor?->getKey(),
            ]);

            CouncilSessionCreated::dispatch($session, $actor);

            $this->lifecycle->register(
                $session->school_id,
                $session->getKey(),
                $session->uuid,
                [
                    'year_id' => $session->academic_calendar_year_id,
                    'term_id' => $session->academic_calendar_term_id,
                    'class_id' => $session->school_class_id,
                    'section_id' => $session->class_section_id,
                ],
                $actor?->getKey(),
                'CouncilSessionCreated',
                $actor,
            );

            return $session;
        });
    }

    private function assertAcademicContext(CreateCouncilSessionData $data): void
    {
        if ($this->academic->period($data->schoolId, $data->yearId, $data->termId, $data->classId, $data->sectionId) === null) {
            throw new ClassCouncilException("Academic frame [year {$data->yearId}, term {$data->termId}] not found.");
        }
    }
}
