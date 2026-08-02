<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Services;

use App\Domains\Academic\Application\Actions\CreateSubjectAction;
use App\Domains\Academic\Application\DTOs\CoefficientData;
use App\Domains\Academic\Application\DTOs\SubjectData;
use App\Domains\Academic\Application\DTOs\SubjectGroupData;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Domains\Academic\Infrastructure\Models\Coefficient;
use App\Domains\Academic\Infrastructure\Models\SubjectGroup;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class SubjectCatalogService
{
    public function __construct(
        private readonly CreateSubjectAction $createSubject,
    ) {
    }

    public function createGroup(SubjectGroupData $data): SubjectGroup
    {
        return SubjectGroup::create($data->toArray());
    }

    public function createSubject(SubjectData $data, ?User $actor = null): AcademicSubject
    {
        return $this->createSubject->execute($data, $actor);
    }

    public function assignCoefficient(CoefficientData $data): Coefficient
    {
        $duplicate = Coefficient::query()
            ->forSchool($data->schoolId)
            ->where('school_class_id', $data->schoolClassId)
            ->where('academic_subject_id', $data->subjectId)
            ->exists();

        if ($duplicate) {
            throw DuplicateResourceException::for('coefficient', 'pair', 'class+subject');
        }

        return DB::transaction(fn (): Coefficient => Coefficient::create($data->toArray()));
    }
}
