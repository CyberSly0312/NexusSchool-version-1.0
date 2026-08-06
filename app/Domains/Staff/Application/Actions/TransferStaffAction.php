<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Application\DTOs\TransferData;
use App\Domains\Staff\Domain\Events\EmploymentTransferred;
use App\Domains\Staff\Domain\Exceptions\EmploymentException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Transfers an active employment between campuses. The contract and its
 * history stay with the same staff; only the owning school changes.
 */
final class TransferStaffAction
{
    public function execute(TransferData $data, ?User $actor = null): Employment
    {
        $staff = Staff::query()->findOrFail($data->staffId);

        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }

        if ($data->fromSchoolId === $data->toSchoolId) {
            throw EmploymentException::message('The source and destination schools must differ.');
        }

        $employment = Employment::query()
            ->forSchool($data->fromSchoolId)
            ->forStaff($staff->getKey())
            ->active()
            ->first();

        if ($employment === null) {
            throw EmploymentException::notFound($staff->getKey());
        }

        $fromSchoolId = $employment->school_id;

        return DB::transaction(function () use ($employment, $data, $fromSchoolId, $actor): Employment {
            $employment->update(['school_id' => $data->toSchoolId]);

            EmploymentTransferred::dispatch($employment, $fromSchoolId, $data->toSchoolId, $actor);

            return $employment;
        });
    }
}
