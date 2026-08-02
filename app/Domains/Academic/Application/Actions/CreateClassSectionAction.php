<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Application\DTOs\ClassSectionData;
use App\Domains\Academic\Domain\Events\ClassSectionCreated;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\ClassSection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateClassSectionAction
{
    public function execute(ClassSectionData $data, ?User $actor = null): ClassSection
    {
        $duplicate = ClassSection::query()
            ->forSchool($data->schoolId)
            ->where('school_class_id', $data->schoolClassId)
            ->where('name', $data->name)
            ->exists();

        if ($duplicate) {
            throw DuplicateResourceException::for('section', 'name', $data->name);
        }

        return DB::transaction(function () use ($data, $actor): ClassSection {
            $section = ClassSection::create($data->toArray());

            ClassSectionCreated::dispatch($section, $actor);

            return $section;
        });
    }
}
