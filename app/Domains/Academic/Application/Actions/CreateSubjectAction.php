<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Application\DTOs\SubjectData;
use App\Domains\Academic\Domain\Events\SubjectCreated;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateSubjectAction
{
    public function execute(SubjectData $data, ?User $actor = null): AcademicSubject
    {
        $duplicateCode = AcademicSubject::query()
            ->forSchool($data->schoolId)
            ->where('code', $data->code)
            ->exists();

        if ($duplicateCode) {
            throw DuplicateResourceException::for('subject', 'code', $data->code);
        }

        $duplicateName = AcademicSubject::query()
            ->forSchool($data->schoolId)
            ->where('name', $data->name)
            ->exists();

        if ($duplicateName) {
            throw DuplicateResourceException::for('subject', 'name', $data->name);
        }

        return DB::transaction(function () use ($data, $actor): AcademicSubject {
            $subject = AcademicSubject::create($data->toArray());

            SubjectCreated::dispatch($subject, $actor);

            return $subject;
        });
    }
}
