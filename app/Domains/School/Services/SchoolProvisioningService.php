<?php

declare(strict_types=1);

namespace App\Domains\School\Services;

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\DTOs\SchoolProvisioningResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates a school and its initial School Admin as one atomic operation.
 *
 * School creation is never considered complete without an administrator: if
 * the administrator cannot be provisioned (duplicate email, invalid input,
 * token persistence failure, ...) the whole transaction rolls back, including
 * the school and its domain row.
 */
class SchoolProvisioningService
{
    public function __construct(
        protected SchoolService $schools = new SchoolService(),
        protected SchoolAdminProvisioningService $administrators = new SchoolAdminProvisioningService(),
    ) {
    }

    public function create(
        SchoolData $data,
        string $adminName,
        string $adminEmail,
        ?User $actor = null,
    ): SchoolProvisioningResult {
        if (trim($adminName) === '') {
            throw ValidationException::withMessages([
                'admin_name' => 'An initial School Admin name is required.',
            ]);
        }

        if (trim($adminEmail) === '') {
            throw ValidationException::withMessages([
                'admin_email' => 'An initial School Admin email address is required.',
            ]);
        }

        return DB::transaction(function () use ($data, $adminName, $adminEmail, $actor) {
            $school = $this->schools->create($data);

            $administrator = $this->administrators->provision(
                $school,
                $adminName,
                $adminEmail,
                $actor,
            );

            return new SchoolProvisioningResult($school, $administrator, $actor);
        });
    }
}
