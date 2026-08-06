<?php

declare(strict_types=1);

namespace App\Domains\Staff\Application\Actions;

use App\Domains\Staff\Application\DTOs\EmploymentData;
use App\Domains\Staff\Domain\Events\EmploymentStarted;
use App\Domains\Staff\Domain\Exceptions\EmploymentException;
use App\Domains\Staff\Domain\Exceptions\InvalidStaffStateException;
use App\Domains\Staff\Domain\ValueObjects\ContractPeriod;
use App\Domains\Staff\Domain\ValueObjects\SalaryScale;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Starts an employment contract for a staff member. At most one active
 * (non-terminated) employment per staff — enforced in application and backed
 * by a partial unique index in the schema.
 */
final class StartEmploymentAction
{
    public function execute(EmploymentData $data, ?User $actor = null): Employment
    {
        $staff = Staff::query()->findOrFail($data->staffId);

        $this->assertMutable($staff);
        $this->assertNoActiveEmployment($staff);

        new ContractPeriod($data->contractStartDate, $data->contractEndDate);

        if ($data->salaryScale !== null) {
            new SalaryScale($data->salaryScale);
        }

        $attributes = $data->toArray();

        if ($attributes['salary_scale'] !== null) {
            $attributes['salary_scale'] = (new SalaryScale((string) $attributes['salary_scale']))->value();
        }

        return DB::transaction(function () use ($attributes, $actor): Employment {
            $employment = Employment::create($attributes);

            EmploymentStarted::dispatch($employment, $actor);

            return $employment;
        });
    }

    private function assertMutable(Staff $staff): void
    {
        if ($staff->isImmutable()) {
            throw InvalidStaffStateException::immutable($staff->getKey());
        }
    }

    private function assertNoActiveEmployment(Staff $staff): void
    {
        $active = Employment::query()
            ->forSchool($staff->school_id)
            ->forStaff($staff->getKey())
            ->active()
            ->exists();

        if ($active) {
            throw EmploymentException::alreadyActive($staff->getKey());
        }
    }
}
