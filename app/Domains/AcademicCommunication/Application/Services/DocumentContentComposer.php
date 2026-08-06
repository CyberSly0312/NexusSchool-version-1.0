<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Services;

use App\Domains\AcademicCommunication\Domain\Contracts\AcademicFrameworkContract;
use App\Domains\AcademicCommunication\Domain\Contracts\SchoolContract;

/**
 * Composes the shared content blocks (school, academic frame) embedded in every
 * rendered AcademicDocument (SPR-012). Typed generation actions build the
 * type-specific sections (results, decisions, â€¦) and merge them with these
 * blocks. All values are model-free references resolved through the engine
 * contracts.
 */
final class DocumentContentComposer
{
    public function __construct(
        private readonly SchoolContract $school,
        private readonly AcademicFrameworkContract $academicFramework,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function schoolBlock(string $schoolId): array
    {
        $schoolReference = $this->school->school($schoolId);

        return [
            'name' => $schoolReference?->name ?? 'Ã‰cole',
            'logo_url' => $schoolReference?->logoUrl,
            'address' => $schoolReference?->fullAddress(),
            'phone' => $schoolReference?->phone,
            'email' => $schoolReference?->email,
            'registration_number' => $schoolReference?->registrationNumber,
            'motto' => $schoolReference?->motto,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function frameBlock(
        string $schoolId,
        int|string|null $yearId = null,
        int|string|null $termId = null,
        int|string|null $classId = null,
        int|string|null $sectionId = null,
    ): array {
        $year = $yearId !== null ? $this->academicFramework->year($schoolId, $yearId) : null;
        $term = $termId !== null ? $this->academicFramework->term($schoolId, $termId) : null;
        $class = $classId !== null ? $this->academicFramework->class($schoolId, $classId) : null;
        $section = $sectionId !== null ? $this->academicFramework->section($schoolId, $sectionId) : null;

        return [
            'year' => $year?->name,
            'year_id' => $year?->id,
            'term' => $term?->name,
            'term_id' => $term?->id,
            'class' => $class?->name,
            'class_id' => $class?->id,
            'section' => $section?->name,
            'section_id' => $section?->id,
        ];
    }
}
