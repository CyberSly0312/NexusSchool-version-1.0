<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Domain\Enums\EmploymentStatus;
use App\Domains\Staff\Domain\Enums\StaffStatus;
use App\Domains\Staff\Domain\Events\EmploymentTerminated;
use App\Domains\Staff\Domain\Exceptions\EmploymentException;
use App\Domains\Staff\Domain\ValueObjects\ContractPeriod;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Terminates an active employment contract. The employment row is never
 * deleted (soft-deletes only); the termination is recorded both on the
 * contract and in the immutable EmploymentHistory timeline. A terminated
 * staff member can no longer be modified.
 */
final class TerminateEmploymentAction
{
    public function execute(
        Employment $employment,
        ?string $reason = null,
        ?string $terminationDate = null,
        ?User $actor = null,
    ): Employment {
        if (! $employment->isActive()) {
            throw EmploymentException::notActive($employment->getKey());
        }

        $terminationDate ??= now()->format('Y-m-d');
        $this->assertValidTerminationDate($employment, $terminationDate);

        return DB::transaction(function () use ($employment, $reason, $terminationDate, $actor): Employment {
            $employment->update([
                'status' => EmploymentStatus::Terminated,
                'termination_date' => $terminationDate,
                'termination_reason' => $reason,
            ]);

            $staff = $employment->staff;
            if ($staff !== null && $staff->status === StaffStatus::Active) {
                $staff->update(['status' => StaffStatus::Terminated]);
            }

            EmploymentTerminated::dispatch($employment, $reason, $terminationDate, $actor);

            return $employment;
        });
    }

    private function assertValidTerminationDate(Employment $employment, string $terminationDate): void
    {
        new ContractPeriod(
            $employment->contract_start_date?->format('Y-m-d') ?? '',
            $terminationDate,
        );
    }
}
