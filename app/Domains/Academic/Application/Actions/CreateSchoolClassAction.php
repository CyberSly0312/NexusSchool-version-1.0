<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Application\DTOs\SchoolClassData;
use App\Domains\Academic\Domain\Events\SchoolClassCreated;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateSchoolClassAction
{
    public function execute(SchoolClassData $data, ?User $actor = null): SchoolClass
    {
        $duplicate = SchoolClass::query()
            ->forSchool($data->schoolId)
            ->where('academic_program_id', $data->programId)
            ->where('name', $data->name)
            ->exists();

        if ($duplicate) {
            throw DuplicateResourceException::for('class', 'name', $data->name);
        }

        return DB::transaction(function () use ($data, $actor): SchoolClass {
            $schoolClass = SchoolClass::create($data->toArray());

            SchoolClassCreated::dispatch($schoolClass, $actor);

            return $schoolClass;
        });
    }
}
