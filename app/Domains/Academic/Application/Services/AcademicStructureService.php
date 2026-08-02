<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Services;

use App\Domains\Academic\Application\Actions\CreateClassSectionAction;
use App\Domains\Academic\Application\Actions\CreateSchoolClassAction;
use App\Domains\Academic\Application\Actions\CreateStreamAction;
use App\Domains\Academic\Application\DTOs\ClassSectionData;
use App\Domains\Academic\Application\DTOs\EducationLevelData;
use App\Domains\Academic\Application\DTOs\ProgramData;
use App\Domains\Academic\Application\DTOs\SchoolClassData;
use App\Domains\Academic\Application\DTOs\StreamData;
use App\Domains\Academic\Infrastructure\Models\AcademicProgram;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Domains\Academic\Infrastructure\Models\EducationLevel;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Domains\Academic\Infrastructure\Models\Stream;
use App\Models\User;

final class AcademicStructureService
{
    public function __construct(
        private readonly CreateStreamAction $createStream,
        private readonly CreateSchoolClassAction $createSchoolClass,
        private readonly CreateClassSectionAction $createClassSection,
    ) {
    }

    public function createLevel(EducationLevelData $data): EducationLevel
    {
        return EducationLevel::create($data->toArray());
    }

    public function createProgram(ProgramData $data): AcademicProgram
    {
        return AcademicProgram::create($data->toArray());
    }

    public function createStream(StreamData $data, ?User $actor = null): Stream
    {
        return $this->createStream->execute($data, $actor);
    }

    public function createClass(SchoolClassData $data, ?User $actor = null): SchoolClass
    {
        return $this->createSchoolClass->execute($data, $actor);
    }

    public function createSection(ClassSectionData $data, ?User $actor = null): ClassSection
    {
        return $this->createClassSection->execute($data, $actor);
    }
}
