<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Domains\User\Enums\EnrollmentProvisioningKind;
use App\Domains\User\Enums\EnrollmentProvisioningRejectionCode;
use App\Models\User;

/**
 * Privacy-safe provisioning outcome.
 *
 * Internal identifiers are included for audits and follow-up workflows. No
 * contact detail, password, token or token hash is exposed.
 */
final readonly class EnrollmentProvisioningResult
{
    /**
     * @param  list<string>  $matchReasonCodes
     */
    public function __construct(
        public User $user,
        public EnrollmentProvisioningKind $kind,
        public bool $userCreated,
        public string $enrollmentId,
        public ?string $studentId,
        public ?string $guardianId,
        public ?EnrollmentInvitationDescriptor $invitation,
        public EnrollmentProvisioningRejectionCode $deliveryBlockReason,
        public array $matchReasonCodes,
    ) {
    }

    public function deliveryBlocked(): bool
    {
        return $this->deliveryBlockReason === EnrollmentProvisioningRejectionCode::ActivationBlocked;
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'kind' => $this->kind->value,
            'userCreated' => $this->userCreated,
            'enrollmentId' => $this->enrollmentId,
            'studentId' => $this->studentId,
            'guardianId' => $this->guardianId,
            'invitation' => $this->invitation?->__debugInfo(),
            'deliveryBlockReason' => $this->deliveryBlockReason->value,
            'matchReasonCodes' => $this->matchReasonCodes,
        ];
    }
}
