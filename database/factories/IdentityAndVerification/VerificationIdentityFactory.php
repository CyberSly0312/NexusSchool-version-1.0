<?php

declare(strict_types=1);

namespace Database\Factories\IdentityAndVerification;

use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VerificationIdentity>
 */
class VerificationIdentityFactory extends Factory
{
    protected $model = VerificationIdentity::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::ulid(),
            'school_id' => 'fac-'.(string) Str::ulid(),
            'academic_document_id' => AcademicDocument::factory(),
            'token_hash' => hash('sha256', Str::random(32)),
            'status' => VerificationStatus::Active,
            'issued_at' => now(),
        ];
    }

    public function forSchool(string $schoolId): static
    {
        return $this->state(fn (): array => ['school_id' => $schoolId]);
    }

    public function forDocument(AcademicDocument $document): static
    {
        return $this->state(fn (): array => [
            'school_id' => $document->school_id,
            'academic_document_id' => $document->getKey(),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (): array => [
            'status' => VerificationStatus::Revoked,
            'revocation_reason' => 'Factory default revocation reason.',
            'revoked_at' => now(),
        ]);
    }

    public function replaced(): static
    {
        return $this->state(fn (): array => [
            'status' => VerificationStatus::Replaced,
            'revocation_reason' => 'Factory default replacement reason.',
            'replaced_at' => now(),
            'replaced_by_document_number' => 'NS-2027-SCH001-RC-999999',
        ]);
    }
}
