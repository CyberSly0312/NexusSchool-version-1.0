<?php

declare(strict_types=1);

namespace App\Domains\Academic\Application\Actions;

use App\Domains\Academic\Application\DTOs\StreamData;
use App\Domains\Academic\Domain\Events\StreamCreated;
use App\Domains\Academic\Domain\Exceptions\DuplicateResourceException;
use App\Domains\Academic\Infrastructure\Models\Stream;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateStreamAction
{
    public function execute(StreamData $data, ?User $actor = null): Stream
    {
        $duplicate = Stream::query()
            ->forSchool($data->schoolId)
            ->where('academic_program_id', $data->programId)
            ->where('name', $data->name)
            ->exists();

        if ($duplicate) {
            throw DuplicateResourceException::for('stream', 'name', $data->name);
        }

        return DB::transaction(function () use ($data, $actor): Stream {
            $stream = Stream::create($data->toArray());

            StreamCreated::dispatch($stream, $actor);

            return $stream;
        });
    }
}
